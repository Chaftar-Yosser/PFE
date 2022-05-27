<?php

namespace App\Form;

use App\Entity\Projects;
use App\Entity\Skills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Nom',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('date_debut',DateType::class,[
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
                    '4 mois' => '4 mois',
                    '8 mois' => '8 mois',
                    '12 mois et +' => '12 mois et +'
                ]

            ])

            ->add('status',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    'En Cours' => 'En Cours',
                    'En Pause' => 'En Pause',
                    'Terminé' => 'Terminé',
                ],
                'attr' => [
                    'class' => "form-control ",
                ]
            ])

            ->add('skills' , EntityType::class,[
                'class' => Skills::class,
                'choice_label' => 'name',
                'label' => 'technologies',
                'attr' => [
                    'class' => "form-control select2 ",
                ],
                'expanded'  => false,
                'multiple'  => true,
            ])
        ;
        if (!$options['create']){
            $builder->add('date_fin',DateType::class,[
                'widget' => 'single_text',
                'attr' => [
                    'class' => "form-control ",
                ],

            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projects::class,
            'create' => false
        ]);
    }
}
