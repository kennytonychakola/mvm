<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Manuscript;
use AppBundle\Form\ManuscriptContentsType;
use AppBundle\Form\ManuscriptContributionsType;
use AppBundle\Form\ManuscriptFeaturesType;
use AppBundle\Form\ManuscriptType;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manuscript controller.
 *
 * @IsGranted("ROLE_USER")
 * @Route("/manuscript")
 */
class ManuscriptController extends Controller implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Manuscript entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="manuscript_index", methods={"GET"})
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Manuscript::class, 'e')->orderBy('e.callNumber', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $manuscripts = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'manuscripts' => $manuscripts,
        );
    }

    /**
     * Typeahead API endpoint for Manuscript entities.
     *
     * @param Request $request
     *
     * @Route("/typeahead", name="manuscript_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse(array());
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Manuscript::class);
        $data = array();
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = array(
                'id' => $result->getId(),
                'text' => (string) $result . ' ' . $result->getCallNumber(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * Search for Manuscript entities.
     *
     * <code><pre>
     *    public function searchQuery($q) {
     *       $qb = $this->createQueryBuilder('e');
     *       $qb->addSelect("MATCH (e.title) AGAINST(:q BOOLEAN) as HIDDEN score");
     *       $qb->orderBy('score', 'DESC');
     *       $qb->setParameter('q', $q);
     *       return $qb->getQuery();
     *    }
     * </pre></code>
     *
     * @param Request $request
     *
     * @Route("/search", name="manuscript_search", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Manuscript');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $manuscripts = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $manuscripts = array();
        }

        return array(
            'manuscripts' => $manuscripts,
            'q' => $q,
        );
    }

    /**
     * Creates a new Manuscript entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/new", name="manuscript_new", methods={"GET","POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $manuscript = new Manuscript();
        $form = $this->createForm(ManuscriptType::class, $manuscript);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($manuscript);
            $em->flush();

            $this->addFlash('success', 'The new manuscript was created.');

            return $this->redirectToRoute('manuscript_show', array('id' => $manuscript->getId()));
        }

        return array(
            'manuscript' => $manuscript,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Manuscript entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/new_popup", name="manuscript_new_popup", methods={"GET","POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Manuscript entity.
     *
     * @param Manuscript $manuscript
     *
     * @return array
     *
     * @Route("/{id}", name="manuscript_show", methods={"GET"})
     * @Template()
     */
    public function showAction(Manuscript $manuscript) {
        return array(
            'manuscript' => $manuscript,
        );
    }

    /**
     * Displays a form to edit an existing Manuscript entity.
     *
     * @param Request $request
     * @param Manuscript $manuscript
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="manuscript_edit", methods={"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, Manuscript $manuscript) {
        $editForm = $this->createForm(ManuscriptType::class, $manuscript);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ('complete' === $request->request->get('submit', '')) {
                $manuscript->setComplete(true);
            } else {
                $manuscript->setComplete(false);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The manuscript has been updated.');

            return $this->redirectToRoute('manuscript_show', array('id' => $manuscript->getId()));
        }

        return array(
            'manuscript' => $manuscript,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Manuscript entity.
     *
     * @param Request $request
     * @param Manuscript $manuscript
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/delete", name="manuscript_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, Manuscript $manuscript) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($manuscript);
        $em->flush();
        $this->addFlash('success', 'The manuscript was deleted.');

        return $this->redirectToRoute('manuscript_index');
    }

    /**
     * Edits a Manuscript's content entities.
     *
     * @param Request $request
     * @param Manuscript $manuscript
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/contents", name="manuscript_contents", methods={"GET", "POST"})
     * @Template()
     */
    public function contentsAction(Request $request, Manuscript $manuscript) {
        $oldContents = $manuscript->getManuscriptContents()->toArray();

        $editForm = $this->createForm(ManuscriptContentsType::class, $manuscript);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $msContents = $manuscript->getManuscriptContents();
            foreach($oldContents as $content) {
                if( ! $msContents->contains($content)) {
                    $manuscript->removeManuscriptContent($content);
                    $em->remove($content);
                }
            }
            foreach ($manuscript->getManuscriptContents() as $content) {
                $content->setManuscript($manuscript);
            }
            $em->flush();
            $this->addFlash('success', 'The manuscript has been updated.');

            return $this->redirectToRoute('manuscript_show', array('id' => $manuscript->getId()));
        }

        return array(
            'manuscript' => $manuscript,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits a Manuscript's contributions entities.
     *
     * @param Request $request
     * @param Manuscript $manuscript
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/contributions", name="manuscript_contributions", methods={"GET", "POST"})
     * @Template()
     */
    public function contributionsAction(Request $request, Manuscript $manuscript) {
        $oldContributions = $manuscript->getManuscriptContributions()->toArray();

        $editForm = $this->createForm(ManuscriptContributionsType::class, $manuscript);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $msContributions = $manuscript->getManuscriptContributions();
            foreach($oldContributions as $contribution) {
                if( ! $msContributions->contains($contribution)) {
                    $manuscript->removeManuscriptContribution($contribution);
                    $em->remove($contribution);
                }
            }
            foreach ($msContributions as $contribution) {
                $contribution->setManuscript($manuscript);
            }
            $em->flush();
            $this->addFlash('success', 'The manuscript has been updated.');

            return $this->redirectToRoute('manuscript_show', array('id' => $manuscript->getId()));
        }

        return array(
            'manuscript' => $manuscript,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits a Manuscript's feature entities.
     *
     * @param Request $request
     * @param Manuscript $manuscript
     *
     * @return array|RedirectResponse
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/features", name="manuscript_features", methods={"GET", "POST"})
     * @Template()
     */
    public function featuresAction(Request $request, Manuscript $manuscript) {
        $oldFeatures = $manuscript->getManuscriptFeatures()->toArray();

        $editForm = $this->createForm(ManuscriptFeaturesType::class, $manuscript);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $msFeatures = $manuscript->getManuscriptFeatures();
            foreach($oldFeatures as $feature) {
                if( ! $msFeatures->contains($feature)) {
                    $manuscript->removeManuscriptFeature($feature);
                    $em->remove($feature);
                }
            }
            foreach ($msFeatures as $feature) {
                $feature->setManuscript($manuscript);
            }
            $em->flush();
            $this->addFlash('success', 'The manuscript has been updated.');

            return $this->redirectToRoute('manuscript_show', array('id' => $manuscript->getId()));
        }

        return array(
            'manuscript' => $manuscript,
            'edit_form' => $editForm->createView(),
        );
    }
}
