<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\InvoiceCompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InvoiceController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/invoice/add", name="add_invoice")
     */
    // public function add(Request $request, SluggerInterface $slugger)
    // {
    //     $invoice = new Invoice();
    //     $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice);
    //     $addInvoiceForm->handleRequest($request);

    //     if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {
    //         $invoice = $addInvoiceForm->getData();
    //         $path = $addInvoiceForm->get('file')->getData();
    //         $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);

    //         if ($path) {
    //             $originalFilename = pathinfo($path->getClientOriginalName(), PATHINFO_FILENAME);
    //             $safeFilename = $slugger->slug($originalFilename);
    //             $newFilename = $safeFilename . '.' . $path->guessExtension();

    //             try {
    //                 $path->move(
    //                     $this->getParameter('invoices'),
    //                     $newFilename
    //                 );
    //             } catch (FileException $e) {
    //                 // ... handle exception if something happens during file upload
    //             }

    //             $invoice->setFile($newFilename)
    //                 ->setCompany($company);
    //             $company->addInvoice($invoice);

    //             $this->em->persist($invoice);
    //             $this->em->persist($company);

    //             $this->em->flush();
    //         }

    //         // Envoie d'un mail de confirmation
    //         return $this->redirectToRoute('email', [
    //             'company' => $company->getId(),
    //             'invoice' => $invoice->getId()
    //         ]);

    //         // return $this->redirectToRoute('admin');
    //     }

    //     $companies = $this->companyRepo->findAll();

    //     return $this->render('invoice/add.html.twig', [
    //         'add_invoice_form' => $addInvoiceForm->createView(),
    //         'companies' => $companies
    //     ]);
    // }

    /**
     * @Route("/invoice/add/{company}", name="add_invoice_with_company")
     */
    public function addForCompany(Request $request, Company $company, SluggerInterface $slugger): Response
    {
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

                    // var_dump($invoice->getFiles());
                    // var_dump($company);

                    $this->em->persist($invoice);
                    $this->em->persist($company);

                    $this->em->flush();
                }
            }

            // Envoie d'un mail de confirmation
            return $this->redirectToRoute('email_invoice_confirmation', [
                'company' => $company->getId(),
                'invoice' => $invoice->getId()
            ]);
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
}
