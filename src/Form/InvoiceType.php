<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\TypeInvoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        // ->add('releasedAt', DateTimeType::class, [
        //     'label' => 'Date de facturation *',
        //     'widget' => 'single_text',
        //     'required' => true,
        //     'attr' => [
        //         'class' => 'form-control'
        //     ]
        // ])
        // ->add('file', FileType::class, [
        //     'label' => 'Fichier PDF *',
        //     'required' => true,
        //     'attr' => [
        //         'class' => 'form-control'
        //     ]
        // ])
        // ->add('type', EntityType::class, [
        //     'class' => TypeInvoice::class,
        //     'choice_label' => 'name',
        //     'required' => true,
        //     'label' => 'Type *',
        //     'attr' => [
        //         'class' => 'form-select'
        //     ]
        // ])
        // ->add('submit', SubmitType::class, [
        //     'label' => 'Envoyer',
        //     'attr' => [
        //         'class' => 'btn-type btn-form'
        //     ]
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
