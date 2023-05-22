<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CreateProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom du projet',
                'required' => true,
                /*             ])
            ->add('users', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Users::class,
                'choice_label' => 'username',
                'label' => 'Utilisateur',
                'required' => true, */
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projects::class,
        ]);
    }
}
