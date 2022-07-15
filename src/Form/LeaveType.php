<?php

namespace App\Form;

use App\Entity\Leave;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LeaveType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!($options['update'] )) {
            $builder
                ->add('startDate', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'date début',
                    'attr' => [
                        'class' => "form-control ",
//                        'disabled' => $options['update'] ? 'disabled' : false // pour que les champs soit non modifiable
                    ],

                ])
                ->add('endDate', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'date fin',
                    'attr' => [
                        'class' => "form-control ",

                    ],
                ])
                ->add('Leave_type', EntityType::class, [
                    'class' => \App\Entity\LeaveType::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'label' => 'type congé',
                    'attr' => [
                        'class' => "form-control ",
                    ]
                ])
                ->add('userTo', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'lastname',
                    'required' => false,
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        $er = $er->createQueryBuilder('u');
                            return
                                $er->orWhere($er->expr()->like('u.role', ':rh'))
                                    ->orWhere($er->expr()->like('u.role', ':admin'))
                                    ->setParameter('rh', '%"ROLE_RH"%')
                                    ->setParameter('admin', '%"ROLE_ADMIN"%');
                    },
                    'label' => 'à',
                    'attr' => [
                        'class' => "form-control ",
                        'disabled' => $options['edit']
                    ]
                ])
                ->add('userFrom', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'lastname',
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        $er = $er->createQueryBuilder('u');
                        if(!$this->authorizationChecker->isGranted("ROLE_ADMIN") && isset($options['currentUser']) && $options['currentUser']){
                            return
                                $er->andWhere($er->expr()->eq('u.id', ':uid'))
                                ->setParameter('uid', $options['currentUser']->getId());
                        }
                        return $er;
                    },
//                    'data' => $options['currentUser'],
                    'required' => false,
                    'label' => 'de',
                    'attr' => [
                        'class' => "form-control ",
                        'disabled' => $options['edit']
                    ]
                ]);
        }
        $builder
            ->add('status',ChoiceType::class,[
                'label' => 'statut',
                'data' => $options["create"] ? "En cours" : "",
                'choices' =>[
                    '' => '' ,
                    'En cours' => 'En cours',
                    'Accepter' => 'Accepter',
                    'Réfuser' => 'Réfuser'
                ],
                'attr' => [
                    'disabled' => $options["create"] ? "disabled" : false,
                    'class' => "form-control ",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Leave::class,
            'update' => false,
            'create' => true,
            'edit'  => false,
            'currentUser' => null
        ]);
    }
}
