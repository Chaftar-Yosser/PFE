<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Skills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title' , TextType::class,[
                'label' => 'nom',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('score' , TextType::class,[
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
            ->add('skills' , EntityType::class,[
                'class' =>  Skills::class,
                'choice_label' => 'name',
                'label' => 'technologie',
                'attr' => [
                    'class' => "form-control ",
                ]
            ])
//            ->add('Save',SubmitType::class,[
//                'attr' => [
//                    'class' => "btn btn-primary ",
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
