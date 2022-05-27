<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Skills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' , TextType::class,[
                'label' => 'Nom de Quiz',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('nombrequestion' , TextType::class,[
                'label' => 'Nombre de question',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('skills' , EntityType::class, [
                'class' =>  Skills::class,
                'choice_label' => 'name',
                'label' => 'Technologies ',
                'attr' => [
                    'class' => "form-control select2",
                ],
                'expanded'  => false,
                'multiple'  => true,
            ])
            ->add('time' , TimeType::class,[
//                'widget' => 'single_text',
                'label' => 'Durée de Quiz',
                'placeholder' => [
                    'hour' => 'Heure', 'minute' => 'Minute'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
