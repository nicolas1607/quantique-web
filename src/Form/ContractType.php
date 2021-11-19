<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\TypeContract;
use App\Entity\User;
use App\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('website', EntityType::class, [
                'class' => Website::class,
                'choice_label' => 'name',
                'label' => 'Site internet',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            // ->add('price', NumberType::class, [
            //     'label' => 'Prix initial',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            // ->add('promotion', NumberType::class, [
            //     'label' => 'Promotion',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            // ->add('type', EntityType::class, [
            //     'class' => TypeContract::class,
            //     'choice_label' => 'name',
            //     'multiple' => true,
            //     'expanded' => true,
            //     'label' => 'Type de contrat',
            //     'attr' => [
            //         'class' => 'form-check'
            //     ]
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-type btn-form'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
