<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\GoogleAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('password', RepeatedType::class, [
            //     'type' => PasswordType::class,
            //     'first_options' => [
            //         'label' => 'Choisir un mot de passe *',
            //         'required' => true,
            //         'attr' => [
            //             'class' => 'form-control'
            //         ]
            //     ],
            //     'second_options' => [
            //         'label' => 'Confirmer le mot de passe *',
            //         'required' => true,
            //         'attr' => [
            //             'class' => 'form-control'
            //         ]
            //     ],
            //     'required' => true
            // ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer',
                'attr' => [
                    'class' => 'btn-type btn-form'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
