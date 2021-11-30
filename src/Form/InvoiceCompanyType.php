<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\TypeInvoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class InvoiceCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('releasedAt', DateType::class, [
                'label' => 'Date de facturation *',
                'required' => true,
                'placeholder' => [
                    'year' => date("Y"), 'month' => 'Month', 'day' => 'Day',
                ],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('files', FileType::class, [
                'label' => 'Fichiers PDF *',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type', EntityType::class, [
                'class' => TypeInvoice::class,
                'required' => true,
                'choice_label' => 'name',
                'label' => 'Type *',
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

        $builder->get('releasedAt')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if (!$value) {
                    return new \DateTime();
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
