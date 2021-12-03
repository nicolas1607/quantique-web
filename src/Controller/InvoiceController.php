<?php

namespace App\Controller;

use DateTime;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\TypeInvoice;
use App\Form\InvoiceType;
use App\Form\InvoiceCompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class InvoiceController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/invoice/add/{company}", name="add_invoice_with_company")
     */
    public function addForCompany(MailerInterface $mailer, Request $request, Company $company, SluggerInterface $slugger): Response
    {
        $invoices = [];
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceCompanyType::class, $invoice);
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {

            $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);

            // Fichier PDF *
            $paths = $addInvoiceForm->get('files')->getData();
            foreach ($paths as $path) {
                $invoice = new Invoice();
                $invoice->setReleasedAt($addInvoiceForm->get('releasedAt')->getData())
                    ->setType($addInvoiceForm->get('type')->getData())
                    ->setCompany($company);

                if ($path) {
                    $originalFilename = pathinfo($path->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '.' . $path->guessExtension();

                    try {
                        $path->move(
                            $this->getParameter('invoices'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $invoice->setFile($newFilename);
                    $company->addInvoice($invoice);

                    $this->em->persist($invoice);
                    $this->em->persist($company);

                    $this->em->flush();
                    $invoices[] = $invoice;
                }
            }

            // Envoie d'un mail de confirmation
            foreach ($company->getUsers() as $user) {
                $email = (new TemplatedEmail())
                    ->from('Quantique Web')
                    ->to($user->getEmail())
                    ->subject('Nouvelle(s) facture(s) pour ' . $company->getName() . ' disponible(s) !')
                    ->htmlTemplate('emails/invoice_confirmation.html.twig')
                    ->context([
                        'user' => $user,
                        'company' => $company
                    ]);
                foreach ($invoices as $invoice) {
                    $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());
                }

                $mailer->send($email);
            }

            return $this->redirectToRoute('show_invoices', ['company' => $company->getId()]);
        }

        return $this->render('invoice/add_with_company.html.twig', [
            'company' => $company,
            'add_invoice_form' => $addInvoiceForm->createView()
        ]);
    }

    /**
     * @Route("/invoice/download/{id}", name="download_invoice")
     */
    public function download(Invoice $id): Response
    {
        $pdfPath = $this->getParameter('invoices') . '/' . $id->getFile();
        return $this->file($pdfPath);
    }

    /**
     * @Route("/invoice/delete/{invoice}", name="delete_invoice")
     */
    public function delete(Invoice $invoice): Response
    {
        $invoice->getCompany()->removeInvoice($invoice);
        $this->em->remove($invoice);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    // MODAL EVENT //

    /**
     * @Route("/invoice/add/modal/{company}", name="add_invoice_modal")
     */
    public function addFromModal(MailerInterface $mailer, Request $request, Company $company, SluggerInterface $slugger): Response
    {
        $invoices = [];

        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice);

        $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);

        // Fichier PDF *
        $paths = $addInvoiceForm->get('files')->getData();

        foreach ($paths as $path) {
            var_dump($path);
            // $path = new UploadedFile($str, str_replace('.pdf', '', $str));
            $invoice = new Invoice();
            $invoice->setReleasedAt(new DateTime($request->get('date')))
                ->setType($this->em->getRepository(TypeInvoice::class)
                    ->findOneBy(['name' => $request->get('type')]))
                ->setCompany($company);

            if ($path) {
                $originalFilename = pathinfo($path->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '.' . $path->guessExtension();

                // $originalFilename = str_replace('.pdf', '', $path);
                // $safeFilename = $slugger->slug($originalFilename);
                // $newFilename = $safeFilename . '.pdf';

                try {
                    $path->move(
                        $this->getParameter('invoices'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $invoice->setFile($newFilename);
                $company->addInvoice($invoice);

                $this->em->persist($invoice);
                $this->em->persist($company);

                $this->em->flush();
                $invoices[] = $invoice;
            }
        }

        // Envoie d'un mail de confirmation
        foreach ($company->getUsers() as $user) {
            $email = (new TemplatedEmail())
                ->from('Quantique Web')
                ->to($user->getEmail())
                ->subject('Nouvelle(s) facture(s) pour ' . $company->getName() . ' disponible(s) !')
                ->htmlTemplate('emails/invoice_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'company' => $company
                ]);
            foreach ($invoices as $invoice) {
                $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());
            }

            $mailer->send($email);
        }

        return $this->redirectToRoute('show_invoices', ['company' => $company->getId()]);
    }
}
