<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Sprint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
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
            ->add('duree',ChoiceType::class,[
                'attr' => [
                    'class' => "form-control ",
                ],
                'choices' =>[
                    '' => '' ,
                    '1 semaine' => '1 semaine',
                    '2 semaine' => '2 semaine',
                    '3 semaine et +' => '3 semaine et +'
                ]
            ])
            ->add('status',ChoiceType::class,[
                'attr' => [
                    'class' => "form-control ",
                ],
                'choices' =>[
                    '' => '' ,
                    'En Cours' => 'En Cours',
                    'En Pause' => 'En Pause',
                    'Terminé' => 'terminé',
                ]
            ])
            ->add('project', EntityType::class,[
                'class' => Projects::class,
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
            'data_class' => Sprint::class,
        ]);
    }
}
