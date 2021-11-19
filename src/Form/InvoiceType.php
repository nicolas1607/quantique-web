<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Website;
use App\Entity\Contract;
use App\Entity\TypeInvoice;
use App\Repository\ContractRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('releasedAt', DateTimeType::class, [
                'label' => 'Date de facturation',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier PDF',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => TypeInvoice::class,
                'choice_label' => 'name',
                'label' => 'Type de facture',
                'attr' => [
                    'class' => 'form-select'
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
            'data_class' => Invoice::class,
        ]);
    }
}
