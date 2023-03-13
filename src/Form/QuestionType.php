<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Repository\CategorieRepository;
use App\Repository\QuestionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class QuestionType extends AbstractType
{

    public function __construct(public ManagerRegistry $registry){
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $question_repository = new QuestionRepository($this->registry);
        $builder
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'row_attr' => [
                    'class' => 'form-floating m-3 text-light',
                ],
                'attr' => [
                    "class" => 'bg-dark text-light border border-dark'
                ],
                'placeholder' => 'Choissisez une catégorie',
                'query_builder' => function (CategorieRepository $categorie_repository){
                    return $categorie_repository->findAllOrderedQueryBuilder();
                }
            ])
            ->add('question', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating m-3 text-light',
                ],
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'bg-dark border border-dark text-light'
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
            'data_class' => Question::class,
        ]);
    }
}
