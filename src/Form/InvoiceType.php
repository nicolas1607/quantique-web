<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Contract;
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
    private User $user;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->user = $options['user'];

        $builder
            ->add('num', TextType::class, [
                'label' => 'Numéro',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
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
            ->add('contract', EntityType::class, [
                'class' => Contract::class,
                'query_builder' => function (ContractRepository $contractRepo) {
                    return $contractRepo->createQueryBuilder('c')
                        ->where('c.user_id = ' . $this->user->getId());
                },
                'choice_label' => 'name',
                'label' => 'Contrat',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-type'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'user' => User::class
        ]);
    }
}
