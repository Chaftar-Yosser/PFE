<?php

namespace App\Form;

use App\Entity\Skills;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
//            ->add('fullname',TextType::class)
            ->add('firstname',TextType::class, [
                'attr' => [
                    'class' => "form-control ",
                    'placeholder' => " admin"
                ],
                'label' => 'Prénom',
            ])
            ->add('lastname',TextType::class, [
                'attr' => [
                    'class' => "form-control ",
                    'placeholder' => " admin"
                ],
                'label' => 'Nom',
            ])
            ->add('email',EmailType::class, [
                'attr' => [
                    'class' => "form-control ",
                    'placeholder' => " admin@admin.fr",
                    'readonly' => $options['edit']
                ]
            ])


            ->add('role',ChoiceType::class,[
                'attr' => [
                    'class' => "form-control select2",
                ],
                'choices' =>[
                    'Développeur' => 'ROLE_DEV',
                    'Rh' => 'ROLE_RH',
                    'admin' =>'ROLE_ADMIN'
                ],
                'expanded' => false,
                'multiple' => true,
                'label' => 'Rôle'
            ])

            ->add('skills' , EntityType::class,[
                'class' => Skills::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => "form-control select2 ",
                ],
                'expanded'  => false,
                'multiple'  => true,
            ]);

             if (!$options['edit']){
                 $builder->add('password',PasswordType::class, [
                     'attr' => [
                         "aria-describedby" =>"basic-default-password2",
                         'placeholder' => " .........",
                         'class' => "form-control ",
                     ],
                     'label' => 'Mot de passe',
                 ])
                     ->add('image',FileType::class,[
                         "data" => $file,
                         'attr' => [
                             'class' => "form-control ",
                         ]
                     ])
                 ;
                 //            test sur affichage de password et image dans creat ctt
             }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'edit'  => false
        ]);
    }
}
