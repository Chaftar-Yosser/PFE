<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Sprint;
use App\Entity\Tasks;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task_name' ,TextType::class,[
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('date_fin',DateType::class)
            ->add('date_debut',DateType::class)
            ->add('duree',ChoiceType::class,[
                'attr' => [
                    'class' => "form-control ",
                ],
                'choices' =>[
                    '' => '' ,
                    '1 heures' => '1 heures',
                    '2 heures' => '2 heures',
                    '3 heures' => '3 heures',
                    '4 heures et +' => '4 heures et +'
                ]

            ])
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    'En Cours' => 'En Cours',
                    'En Pause' => 'En Pause',
                    'Terminé' => 'terminé',
                ],
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('priorite',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    'Elevée' => 'Elevée',
                    'Moyenne' => 'Moyenne',
                    'Normal' => 'Normal',
                ],
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('Projects', EntityType::class,[
                'class' => Projects::class,
                'choice_label'  => 'name',
                'attr' => [
                    'class' => "form-control ",

                ]
            ])
            ->add('users' , EntityType::class, [
                'class' => User::class,
                'choice_label'  => 'lastname',
                'attr' => [
                    'class' => "form-control  select2",
                ],
                'expanded'  => false,
                'multiple'  => true,
            ])
            ->add('sprint', EntityType::class,[
                'class' => Sprint::class,
                'choice_label'  => 'name',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
