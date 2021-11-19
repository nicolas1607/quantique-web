<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du site *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('url', TextType::class, [
                'label' => 'URL du site *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('company', EntityType::class, [
            //     'class' => Company::class,
            //     'choice_label' => 'name',
            //     'label' => 'Entreprise',
            //     'attr' => [
            //         'class' => 'form-select'
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
            'data_class' => Website::class,
        ]);
    }
}
