<?php

namespace App\Form;

use App\Entity\Projects;
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
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('date_debut',DateType::class)
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
                    'Terminé' => 'terminé',
                ],
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
        ;
//        if (!$options['create']){
            $builder->add('date_fin',DateType::class);
//        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projects::class,
        ]);
    }
}
