<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Search;
use App\Entity\Sprint;
use App\Entity\TypeContrat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sprint' , EntityType::class,[
                'class' => Sprint::class,
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Sprint :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('projects', EntityType::class,[
                'class' => Projects::class,
                'choice_label'  => 'name',
                'required' => false,
                'label' => 'Projet :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('status', ChoiceType::class,[
                'choices' =>[
                    'En Cours' => 'En Cours',
                    'En Pause' => 'En Pause',
                    'Terminé' => 'terminé',
                ],
                'required' => false,
                'label' => 'Status :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('role',ChoiceType::class,[
                'choices' =>[
                    'Développeur' => 'ROLE_DEV',
                    'Rh' => 'ROLE_RH',
                    'Admin' =>'ROLE_ADMIN'
                ],
                'required' => false,
                'label' => 'Rôle :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])

            ->add('user', EntityType::class,[
                'class' => User::class,
                'choice_label'  => 'lastname',
                'required' => false,
                'label' => 'user :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('typeContrat', EntityType::class,[
                'class' => TypeContrat::class,
                'choice_label'  => 'name',
                'required' => false,
                'label' => 'type contrat :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('userFrom', EntityType::class,[
                'class' => User::class,
                'choice_label'  => 'lastname',
                'required' => false,
                'label' => 'user :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
            ->add('Leave_type', EntityType::class,[
                'class' => \App\Entity\LeaveType::class,
                'choice_label'  => 'name',
                'required' => false,
                'label' => 'type congés :',
                'attr' => [
                    'placeholder' => false,
                    'class' => "form-control ",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return  '';
    }
}
