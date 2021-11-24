<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\TypeContract;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContractAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => TypeContract::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Type *',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('numIdent', NumberType::class, [
                'label' => 'Identifiant Google ou Facebook',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix initial *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('promotion', NumberType::class, [
                'label' => 'Prix réduit',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
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
