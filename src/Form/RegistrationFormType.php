<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('handle', TextType::class, [
                'label' => "Nom d'utilisateur",
                'required'   => true,
                'constraints' =>[
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Votre nom d'utilisateur contient moins de {{ limit }} caractères.",
                        'maxMessage' => "Votre nom d'utilisateur contient plus de {{ limit }} caractères.",
                    ]),
                    new Assert\Regex([
                        'pattern' => "/^[a-zA-Z0-9\-\_]+$/",
                        'match' => true,
                        'message' => "Votre nom d'utilisateur n'est pas valide.",
                    ])
                ],           
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'text-light bg-dark border border-dark',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-3 text-light',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' =>[
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => "Votre adresse email contient plus de {{ limit }} caractères.",
                    ]),
                    new Assert\Email([
                        'message' => "Votre adresse email n'est pas valide.",
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'text-light bg-dark border border-dark',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-3 text-light',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' =>[
                    new Assert\Length([
                        'min' => 8,
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'minMessage' => "Votre mot de passe contient moins de {{ limit }} caractères.",
                    ])
                ],
                'required' => true,
                'options' => [
                        'attr' => [
                            'placeholder' => 'Name',
                            'class' => 'text-light bg-dark border border-dark',
                        ],
                        'row_attr' => [
                            'class' => 'form-floating m-3 text-light',
                        ]
                ],
                'first_options'  => [
                    'label' => 'Mot de passe', 
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe'
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'btn btn-dark m-3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => user::class,
        ]);
    }
}
