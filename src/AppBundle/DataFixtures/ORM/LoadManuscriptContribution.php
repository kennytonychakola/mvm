<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ManuscriptContribution;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Generated by Webonaute\DoctrineFixtureGenerator.
 */
class LoadManuscriptContribution extends Fixture implements DependentFixtureInterface {
    /**
     * Set loading order.
     *
     * @return int
     */
    public function getDependencies() {
        return array(
            LoadPerson::class,
            LoadManuscript::class,
            LoadManuscriptRole::class,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $manager->getClassMetadata(ManuscriptContribution::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $item1 = new ManuscriptContribution();
        $item1->setNote("<p>\"Property of M Simpson\" handwritten on the front page with date \"Summer '75\"</p>");
        $item1->setPerson($this->getReference('_reference_Person1'));
        $item1->setRole($this->getReference('_reference_ManuscriptRole1'));
        $item1->setManuscript($this->getReference('_reference_Manuscript1'));
        $item1->setCreated(new \DateTime('2019-07-29 22:50:42'));
        $item1->setUpdated(new \DateTime('2019-07-29 22:50:42'));
        $this->addReference('_reference_ManuscriptContribution1', $item1);
        $manager->persist($item1);

        $manager->flush();
    }
}