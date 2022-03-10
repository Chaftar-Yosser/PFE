<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $builder->getData() ? $builder->getData() : null;

        $file = $user && $user->getImage() ? new File($user->getImage(), false) : null ;
        $builder
            ->add('fullname',TextType::class)
            ->add('firstname',TextType::class)
            ->add('lastname',TextType::class)
            ->add('email',EmailType::class)
            ->add('password',PasswordType::class)
            ->add('image',FileType::class,[
                "data" => $file
            ])
            ->add('role',ChoiceType::class,[
                'choices' =>[
                    'Développeur' => 'ROLE_DEV',
                    'Rh' => 'ROLE_RH'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'role'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
