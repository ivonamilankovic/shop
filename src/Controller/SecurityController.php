<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_home_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This should not be reached!!!');
    }

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em, MailerInterface $mailer, VerifyEmailHelperInterface $emailHelper):Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('app_home_home');
        }

        $user = new User();
        $form = $this->createForm(SignupFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $signature = $emailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id'=>$user->getId()]
            );

            $email = (new TemplatedEmail())
                ->from(new Address('web@test.com', 'Web page'))
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->subject('Email verification')
                ->htmlTemplate('email/verifyEmail.html.twig')
                ->context([
                    'verification_link' => $signature->getSignedUrl()
                ])
            ;

            try {
                $mailer->send($email);
            }catch (TransportExceptionInterface $e)
            {
                $this->addFlash('error','Email could not be sent! Try again later.');
            }

            $this->addFlash('success', 'Your profile is successfully created! To be able to log in you have to verify your account via the link in the mail we sent you.');
            return $this->redirectToRoute('app_home_home');
        }

        return $this->render('security/signup.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyEmail(Request $request, VerifyEmailHelperInterface $emailHelper, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if(!$user){
            throw $this->createNotFoundException('User not found!');
        }

        try {
            $emailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        }catch (VerifyEmailExceptionInterface $e){
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_home_home');
        }

        $user->setIsVerified(true);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'You have successfully verified your account. Now you can log in!');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/verify/{id}", name="app_not_verified")
     */
    public function notVerified(UserRepository $repository, int $id):Response
    {
        $user = $repository->findOneBy(['id' => $id]);

        return $this->render('security/notVerified.html.twig',[
            'user' => $user
        ]);
    }

    /**
     * @Route("/verify/resend/{id}", name="app_resend_verify")
     */
    public function resendVerifyEmail(UserRepository $userRepository, int $id, MailerInterface $mailer, VerifyEmailHelperInterface $emailHelper):Response
    {
        $user = $userRepository->findOneBy(['id'=>$id]);

        $signature = $emailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $email = (new TemplatedEmail())
            ->from( new Address('web@test.com', 'Web page'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Email Verification')
            ->htmlTemplate('email/verifyEmail.html.twig')
            ->context([
                'verification_link'=>$signature->getSignedUrl()
            ]);

        try {
            $mailer->send($email);
            $this->addFlash('success', 'New verification mail has been sent. Click on the link in mail to be able to log in.');
        } catch (TransportException $e){
            $this->addFlash('error', 'Email could not be sent! Try again later.');
        }

        return $this->render('security/notVerified.html.twig',[
            'user' => $user
        ]);
    }

}