<?php

namespace SimplyStream\SoulsDeathBundle\Form;

use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrackerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('commandName', TextType::class)
            ->add('sections', CollectionType::class, [
                'entry_type' => SectionType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tracker::class,
        ]);
    }
}
