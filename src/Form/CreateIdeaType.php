<?php

namespace App\Form;

use App\Entity\Ideas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateIdeaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom de l\'idée',
                'required' => true,
                /*             ])
            ->add('branche', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Branches::class,
                'choice_label' => 'name',
                'label' => 'Liaison avec la branche :',
                'required' => true, */
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ideas::class,
        ]);
    }
}
