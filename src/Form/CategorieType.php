<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => "Nom du quizz",
            'required'   => true,
            'constraints' =>[
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => "Le nom de quizz contient plus de {{ limit }} caractères.",
                ]),
            ],           
            'attr' => [
                'placeholder' => 'Name',
                'class' => 'text-light bg-dark border border-dark'
            ],
            'row_attr' => [
                'class' => 'form-floating m-3 text-light',
            ],
        ])
        ->add('imageFile', FileType::class, [
            'label' => "Image du quizz",
            'label_attr' => [
                "class" => "m-3 fw-semibold",
            ],
            'mapped' => false,
            'attr' => [
                'placeholder' => 'Name',
                'class' => 'text-light bg-dark mx-3 border border-dark'
            ],
            'constraints' => [
                new Assert\Image([
                    'mimeTypes' => [
                        "image/png",
                        "image/jpeg",
                        "image/jpg",
                    ],
                    'minWidth' => 200,
                    'minWidthMessage' => 'Sélectionner une image plus grande.',
                    'corruptedMessage' => 'Votre image est corrompu. Sélectionnez-en une autre.',
                    'mimeTypesMessage' => "Ce fichier n'est pas une image valide. Seuls les images de type {{ types }} sont autorisées.",
                ])
            ],
        ])
        ->add('save', SubmitType::class, [
            'label' => "Créer",
            'attr' => [
                'class' => 'btn btn-dark m-3',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
