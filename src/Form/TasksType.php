<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Sprint;
use App\Entity\Tasks;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $task = $builder->getData();
        $builder
            ->add('task_name' ,TextType::class,[
                'label' => 'nom',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('date_fin',DateType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => "form-control ",
                ],

            ])
            ->add('date_debut',DateType::class,[
                'label' => 'daate début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => "form-control ",
                ],

            ])
            ->add('duree',ChoiceType::class,[
                'label' => 'durée',
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
                'label' => 'priorité',
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

            ->add('users' , EntityType::class, [
                'class' => User::class,
                'choices' => $task->getProjects()->getUsers(), // just les users affecter à ce projet
                'choice_label'  => 'lastname',
                'label' => 'utilisateurs',
                'attr' => [
                    'class' => "form-control  select2",
                ],
                'expanded'  => false,
                'multiple'  => true,
            ])
            ->add('description' ,TextareaType::class,[
                'required' => false,
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('avancement',ChoiceType::class,[
                'attr' => [
                    'class' => "form-control ",
                ],
                'required' => false,
                'choices' =>[
                    '10%' => 10,
                    '20%' => 20,
                    '30%' => 30,
                    '40%' => 40,
                    '50%' => 50,
                    '60%' => 60,
                    '70%' => 70,
                    '80%' => 80,
                    '90%' => 90,
                    '100%' => 100,
                ],
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
