<?php

namespace App\Form;

use App\Entity\Compagny;
use App\Entity\Contract;
use App\Entity\Type;
use DateTimeImmutable;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du contrat',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('releasedAt', DateTimeImmutable::class, [
                'label' => 'Date de création',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', FloatType::class, [
                'label' => 'Prix de la préstation',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type_id', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
                'label' => 'Type de contrat',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('user_id', EntityType::class, [
                'class' => Compagny::class,
                'choice_label' => 'name',
                'label' => 'Type de contrat',
                'attr' => [
                    'class' => 'form-control'
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
