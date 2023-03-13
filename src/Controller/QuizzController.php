<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\ReponseType;
use App\Form\CategorieType;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class QuizzController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine, 
        SluggerInterface $slugger,
    ): Response
    {

        $categorie = new Categorie();
        $reponse = new Reponse();
        $question = new Question();
        $manager = $doctrine->getManager();

        $categorie_form = $this->createForm(CategorieType::class, $categorie, [
            'attr' => [
                'class' => 'm-5',
            ],
        ]);
        $reponse_form = $this->createForm(ReponseType::class, $reponse, [
            'attr' => [
                'class' => 'mb-5 mx-5',
            ], 
        ]);   
        $question_form = $this->createForm(QuestionType::class, $question, [
            'attr' => [
                'class' => 'mb-5 mx-5',
            ], 
        ]);         


        $categorie_form->handleRequest($request);
        $question_form->handleRequest($request);
        $reponse_form->handleRequest($request);

        if ($categorie_form->isSubmitted() && $categorie_form->isValid()) {
            $image = $categorie_form['imageFile']->getData();

            if ($image)
            {
                $this->addFlash('success', 'Votre quizz a bien été créé !');
                $manager->persist($categorie);
                $manager->flush();
    
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $categorie->getId().'.jpg';
                try {
                    $image->move(
                        $this->getParameter('quizz_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Une erreur est survenue, l'image du quizz n'a pas pu être stockée.");
                }
            }
            return $this->redirectToRoute('app_create');
        }

        if ($question_form->isSubmitted() && $question_form->isValid()) {
            $this->addFlash('success', 'Votre question a bien été créé !');
            $manager->persist($question);
            $manager->flush();
        }

        if ($reponse_form->isSubmitted() && $reponse_form->isValid()) {
            $id_question = $reponse->getQuestion()->getId();
            $reponse_expected = $manager->getRepository(Reponse::class)
                ->findReponseExpected($id_question);


            if (count($reponse_expected) == 0)
            {
                $this->addFlash('success', 'Votre réponse a bien été créée !');
                $manager->persist($reponse);
                $manager->flush();
            }
            else
            {
                $this->addFlash('danger', 'Une seule bonne réponse est attendu par question.');
            }
        }

        return $this->renderForm('user/create.html.twig', [
            'ReponseForm' => $reponse_form,
            'CategorieForm' => $categorie_form,    
            'QuestionForm' => $question_form
        ]);
    }
}
