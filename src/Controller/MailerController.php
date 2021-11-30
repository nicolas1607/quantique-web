<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @Route("/email/{company}/{invoice}", name="email_invoice_confirmation")
     */
    // public function invoiceConfirmation(MailerInterface $mailer, Company $company, Invoice $invoice): Response
    // {
    //     foreach ($company->getUsers() as $user) {
    //         $email = (new TemplatedEmail())
    //             ->from('nicolas160796@gmail.com')
    //             ->to($user->getEmail())
    //             ->subject('Une nouvelle facture pour ' . $company->getName() . ' est disponible !')
    //             ->htmlTemplate('emails/invoice_confirmation.html.twig')
    //             ->context([
    //                 'user' => $user,
    //                 'company' => $company
    //             ]);
    //         $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());

    //         $mailer->send($email);
    //     }

    //     return $this->redirectToRoute('admin_users');
    // }
}
