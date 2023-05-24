<?php

namespace App\Form;

use App\Entity\Universes;
use App\Entity\Projects;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateUniverseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom de l\'univers',
                'required' => true,
                /*             ])
            ->add('project', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Projects::class,
                'choice_label' => 'name',
                'label' => 'Liaison avec le projet :',
                'required' => true, */
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Universes::class,
        ]);
    }
}
