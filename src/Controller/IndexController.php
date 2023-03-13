<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Entity\Question;
use App\Entity\Score;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(ManagerRegistry $manager): Response
    {
        $quizz = $manager->getRepository(Categorie::class)->findAllDesc();

        return $this->render('user/index.html.twig', [
            'quizz' => $quizz
        ]);
    }

    #[Route('/quizz/{id}', name: 'app_quizz')]
    public function show(
        int $id, 
        Request $request,
        ManagerRegistry $manager): Response
    {
        $user = $this->getUser();
        $categorie = $manager->getRepository(Categorie::class)->find($id);
        $questions = $manager->getRepository(Question::class)->findAllById($id);
        $reponses = $manager->getRepository(Reponse::class);

        $user === null ? $id_user = null : $id_user = $user->getId();

        if (count($manager->getRepository(Score::class)->findLastScoreUser($id, $id_user)) > 0)
        {
            $last_score = $manager->getRepository(Score::class)->findLastScoreUser($id, $id_user)[0]->getScore();
        }
        else
        {
            $last_score = null;
        }

        return $this->render('user/quizz.html.twig', [
            'categorie' => $categorie,
            'reponses' => $reponses,
            'questions' => $questions,
            'id' => $id,
            'last_score' => $last_score,
        ]);
    }
}
