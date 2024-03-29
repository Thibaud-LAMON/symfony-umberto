<?php

namespace App\Form;

use App\Entity\Branches;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateBrancheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom de la branche',
                'required' => true,
                /*             ])
            ->add('universe', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Universes::class,
                'choice_label' => 'name',
                'label' => 'Liaison avec l'univers :',
                'required' => true, */
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Branches::class,
        ]);
    }
}
