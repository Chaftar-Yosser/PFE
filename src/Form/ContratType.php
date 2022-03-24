<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\TypeContrat;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    'En Cours' => 'En Cours',
                    'Résilié' => 'Résilié'
                ],
                    'attr' => [
                        'class' => "form-control ",
                    ]
            ])
            ->add('duree',ChoiceType::class,[
                'choices' =>[
                    '' => '' ,
                    '1 à 6 mois' => '1 à 6 mois',
                    '7 à 12 mois' => '7 à 12 mois',
                    '13 mois et +' => '13 mois et +'
                ],
                    'attr' => [
                        'class' => "form-control ",
                    ]
            ])
            ->add('typeContrat', EntityType::class, [
                'class' =>  TypeContrat::class,
                'choice_label' => 'name',
                    'attr' => [
                        'class' => "form-control ",
                    ]
            ])
            ->add('user', EntityType::class, [
                'class' =>  User::class,
                'choice_label' => 'lastname',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('date_debut',DateType::class,[
//                'attr' => [
//                    'class' => "form-control ",
//                ]
            ]);

        if (!$options['create']){
            $builder->add('date_fin',DateType::class);
            //            test sur affichage de date fin dans edit ctt
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
            'create' => false
        ]);
    }
}
