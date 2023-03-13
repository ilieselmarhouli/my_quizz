<?php

namespace App\Form;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('question', EntityType::class, [
            'class' => Question::class,
            'attr' => [
                'placeholder' => 'Name',
                'class' => 'text-light bg-dark border border-dark'
            ],
            'row_attr' => [
                'class' => 'form-floating m-3 text-light',
            ],  
            'placeholder' => 'Choissisez une question',

        ])
        ->add('reponse', TextType::class, [
            'label' => "Réponse",
            'required'   => true,
            'constraints' =>[
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => "Votre réponse est trop longue.",
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
        ->add('reponse_expected', CheckboxType::class, [
            'label' => 'Réponse correct',
            'attr' => [
                'placeholder' => 'Name',
                'class' => 'text-light bg-dark border border-dark'
            ],
            'row_attr' => [
                'class' => 'form-floating m-3',
            ],  
            'required' => false,
        ])                 
        ->add('save', SubmitType::class, [
            'label' => "Créer",
            'attr' => [
                'class' => 'btn btn-dark m-3',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
