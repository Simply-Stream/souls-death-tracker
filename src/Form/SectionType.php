<?php

namespace SimplyStream\SoulsDeathBundle\Form;

use SimplyStream\SoulsDeathBundle\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('causes', CollectionType::class, [
                'entry_type' => CauseType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__NAME__',
                'property_path' => 'deaths',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
