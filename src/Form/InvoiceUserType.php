<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Contract;
use App\Repository\CompanyRepository;
use App\Repository\ContractRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class InvoiceUserType extends AbstractType
{
    private User $user;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->user = $options['user'];

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
            ->add('contract', EntityType::class, [
                'class' => Company::class,
                'query_builder' => function (CompanyRepository $contractRepo) {
                    return $contractRepo->createQueryBuilder('c')
                        ->where('c.users in (' . $this->user->getId() . ')');
                },
                'choice_label' => 'name',
                'label' => 'Entreprise',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('contract', EntityType::class, [
                'class' => Contract::class,
                // 'query_builder' => function (ContractRepository $contractRepo) {
                //     return $contractRepo->createQueryBuilder('c')
                //         ->join('App:company', 'cmp')
                //         ->where('cmp.users in (' . $this->user->getId() . ')');
                // },
                'choice_label' => 'name',
                'label' => 'Contrat',
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
            'user' => User::class
        ]);
    }
}
