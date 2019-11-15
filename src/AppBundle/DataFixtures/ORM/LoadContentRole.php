<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ContentRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Generated by Webonaute\DoctrineFixtureGenerator.
 */
class LoadContentRole extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $manager->getClassMetadata(ContentRole::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $item1 = new ContentRole();
        $item1->setCreated(new \DateTime('2019-07-29 22:08:59'));
        $item1->setUpdated(new \DateTime('2019-07-29 22:08:59'));
        $item1->setName('author');
        $item1->setLabel('Author');
        $item1->setDescription('<p>The person most responsible for the creation of a work.</p>');
        $this->addReference('_reference_ContentRole1', $item1);
        $manager->persist($item1);

        $manager->flush();
    }
}