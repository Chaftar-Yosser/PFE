<?php

namespace App\Form;

use App\Entity\Leave;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate',DateType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => "form-control ",
                ],

            ])
            ->add('endDate',DateType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => "form-control ",
                ],

            ])
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    'En cours' => 'En cours',
                    'Accepter' => 'Accepter',
                    'Réfuser' => 'Réfuser'
                ],
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('Leave_type', EntityType::class, [
                'class' =>  \App\Entity\LeaveType::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('userTo', EntityType::class, [
                'class' =>  User::class,
                'choice_label' => 'lastname',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('userFrom', EntityType::class, [
                'class' =>  User::class,
                'choice_label' => 'lastname',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Leave::class,
        ]);
    }
}
