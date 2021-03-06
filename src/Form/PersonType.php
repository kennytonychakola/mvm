<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Person;
use App\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * PersonType form.
 */
class PersonType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('anonymous', CheckboxType::class, [
            'label' => 'Anonymous',
            'required' => false,
            'attr' => [
                'help_block' => 'Is the person anonymous?',
            ],
        ]);

        $builder->add('fullName', null, [
            'label' => 'Full Name',
            'required' => true,
            'attr' => [
                'help_block' => 'A canonical name for a person if known, or a descriptive identifier. Do not include square brackets.',
            ],
        ]);
        $builder->add('variantNames', null, [
            'label' => 'Variant Names',
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'entry_type' => TextType::class,
            'entry_options' => [
                'label' => false,
            ],
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'collection collection-simple',
            ],
        ]);

        $builder->add('sortableName', null, [
            'label' => 'Sortable Name',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);

        $builder->add('gender', ChoiceType::class, [
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'choices' => [
                'Female' => Person::FEMALE,
                'Male' => Person::MALE,
                'Unknown' => '',
            ],
            'preferred_choices' => [Person::FEMALE, Person::MALE],
        ]);

        $builder->add('description', null, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);

        $builder->add('birthDate', TextType::class, [
            'label' => 'Birth Date',
            'required' => false,
            'attr' => [
                'help_block' => 'A four digit year, if known for certain. Uncertain date ranges (1901-1903) and circa dates (c1902) are supported here',
            ],
        ]);
        $builder->add('deathDate', TextType::class, [
            'label' => 'Death Date',
            'required' => false,
            'attr' => [
                'help_block' => 'A four digit year, if known for certain. Uncertain date ranges (1901-1903) and circa dates (c1902) are supported here',
            ],
        ]);
        $builder->add('regions', Select2EntityType::class, [
            'label' => 'Regions',
            'multiple' => true,
            'remote_route' => 'region_typeahead',
            'class' => Region::class,
            'required' => false,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'region_new_popup',
                'add_label' => 'Add Region',
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Person',
        ]);
    }
}
