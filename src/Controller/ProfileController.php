<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Score;
use App\Entity\User;
use App\Form\ProfileType;
use App\Security\EmailVerifier;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class ProfileController extends AbstractController
{
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request, 
        ManagerRegistry $doctrine, 
        UserPasswordHasherInterface $passwordHasher,
        ): Response
    {
        $user = $this->getUser();
        $handle = $user->getHandle();
        $user_mail = $user->getEmail();
        $user_password = $user->getPassword();
        $manager = $doctrine->getManager();

        $score = $manager->getRepository(Score::class)->findBy(
            ["user" => $user->getId()],
            ["created_at" => "DESC"]);


        $form = $this->createForm(ProfileType::class, $user, [
            'attr' => [
                'class' => 'm-5',
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user_mail != $form->get("email")->getData())
            {
                $user->setIsVerified(0);
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no.reply@trinity.jp', 'Trinity Corporation'))
                    ->to($user->getEmail())
                    ->subject('Modification de votre email MyQuizz')
                    ->htmlTemplate('email/change_mail.html.twig')
                    ->context(compact('handle'))
                );
            }
            if ($form->get('plainPassword')->getData() !== null)
            {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                );
                if ($user_password != $hashedPassword)
                {
                    $user->setPassword($hashedPassword);
                }
            }

            $this->addFlash('success', 'Vos modifications ont bien été enregistrées !');
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("app_profile");
        }

        return $this->renderForm('profile/index.html.twig',[
            'form' => $form,
            'score' => $score,
            'categorie' => $manager->getRepository(Categorie::class),
        ]);
    }
}
