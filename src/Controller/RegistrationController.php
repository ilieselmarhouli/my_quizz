<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
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

class RegistrationController extends AbstractController
{

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function new(
        Request $request, 
        ManagerRegistry $doctrine, 
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        // creates a task object and initializes some data for this example
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'attr' => [
                'class' => 'm-5',
            ],
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $handle = $user->getHandle();
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(["ROLE_USER"]);

            $manager->persist($user);
            $manager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no.reply@trinity.jp', 'Trinity Corporation'))
                    ->to($user->getEmail())
                    ->subject('Confirmer votre compte MyQuizz !')
                    ->htmlTemplate('email/welcome.html.twig')
                    ->context(compact('handle'))
            );

            $this->addFlash('success', 'Votre compte a bien été créé ! Un email de confirmation vient de vous être envoyé.');

            return $this->redirectToRoute('app_index');

        }

        return $this->renderForm('security/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_index');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse email a bien été verifiée !');

        return $this->redirectToRoute('app_index');
    }
    
    public function email()
    {
        return $this->render("email/welcome.html.twig", [
        ]); 
    }
}