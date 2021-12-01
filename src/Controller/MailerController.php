<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Invoice;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\MessageDigestPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class MailerController extends AbstractController
{
    /**
     * @Route("/email/{user}", name="email_user_confirmation")
     */
    public function userConfirmation(MailerInterface $mailer, User $user): Response
    {
        $email = (new TemplatedEmail())
            ->from('nicolas160796@gmail.com')
            ->to($user->getEmail())
            ->subject('Accédez à votre compte Quantique Web Office !')
            ->htmlTemplate('emails/user_confirmation.html.twig')
            ->context([
                'user' => $user,
            ]);

        $mailer->send($email);

        return $this->redirectToRoute('admin_users');
    }
}
