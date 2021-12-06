<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    // public function buildForm(FormBuilderInterface $builder, array $options): void
    // {
    //     $builder
    //         ->add('message', TextareaType::class, [
    //             'label' => 'Message',
    //             'attr' => [
    //                 'class' => 'form-control'
    //             ]
    //         ])
    //         ->add('submit', SubmitType::class, [
    //             'label' => 'Créer',
    //             'attr' => [
    //                 'class' => 'btn-type btn-form'
    //             ]
    //         ]);
    // }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Note::class,
    //     ]);
    // }
}
