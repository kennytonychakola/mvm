<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Period;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Generated by Webonaute\DoctrineFixtureGenerator.
 */
class LoadPeriod extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $manager->getClassMetadata(Period::class)->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $item1 = new Period();
        $item1->setCreated(new \DateTime('2019-07-29 22:17:32'));
        $item1->setUpdated(new \DateTime('2019-07-29 22:17:32'));
        $item1->setName('1700');
        $item1->setLabel('1700-1729');
        $item1->setDescription('<p>It was a very George II sort of time.</p>');
        $this->addReference('_reference_Period1', $item1);
        $manager->persist($item1);

        $manager->flush();
    }
}
