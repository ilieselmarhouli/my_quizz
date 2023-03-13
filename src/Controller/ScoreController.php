<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\Score;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScoreController extends AbstractController
{
    public function __construct()
    {
        $this->count = 0;
        $this->score =  0;
    }

    #[Route('/score/{id}', name: 'app_score')]
    public function index(
        int $id,
        Request $request, 
        ManagerRegistry $doctrine, 
    ): Response
    {

        if (str_contains($request->getPathInfo(), "score"))
        {
            return $this->redirectToRoute("app_quizz", ["id" => $id]);
        }
        $score = new Score();
        $user = $this->getUser();
        $manager = $doctrine->getManager();
        $categorie = $manager->getRepository(Categorie::class)->find($id);
        $reponses = $manager->getRepository(Reponse::class);
        $questions = $manager->getRepository(Question::class)->findAllById($id);
        $reponses_expected = $manager->getRepository(Reponse::class)->expected($id);
        
        $user === null ? $id_user = null : $id_user = $user->getId();
        
        if (count($manager->getRepository(Score::class)->findLastScoreUser($id, $id_user)) > 0)
        {
            $last_score = $manager->getRepository(Score::class)->findLastScoreUser($id, $id_user)[0]->getScore();
        }
        else
        {
            $last_score = null;
        }
        // dd($last_score);

        $score->setCreatedAt(new \DateTimeImmutable());
        $score->setCategorie($categorie);
        $score->setResult($_POST);
        $score->setuser($user);

        $array_expected = [];

        foreach ($reponses_expected as $expected)
        {
            if ($expected->isReponseExpected() == 1)
            {
                $array_expected[] = $expected->getId();
            }
        }

        if (count($_POST) > 0)
        {
            foreach ($_POST as $key => $value) {
                if (in_array($value, $array_expected))
                {
                    $this->score++;
                }
                $this->count++;
                unset($_POST[$key]);
            }
            $score->setScore("$this->score/$this->count");

            $manager->persist($score);
            $manager->flush();

            return $this->render('user/quizz.html.twig', [
                'categorie' => $categorie,
                'reponses' => $reponses,
                'questions' => $questions,
                'id' => $id,
                'score' => $this->score,
                'count' => $this->count,
                'expecteds' => $array_expected,
                'results' => $score->getResult(),
                'last_score' => $last_score,
            ]);
        }
        else 
        {
            return $this->redirectToRoute("app_index");
        }
    }
}
