<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Form\InvoiceCompanyType;
use App\Repository\CompanyRepository;
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
    private CompanyRepository $companyRepo;

    public function __construct(EntityManagerInterface $em, CompanyRepository $companyRepo)
    {
        $this->em = $em;
        $this->companyRepo = $companyRepo;
    }

    /**
     * @Route("/invoice/add", name="add_invoice")
     */
    public function add(Request $request, SluggerInterface $slugger)
    {
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice);
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {
            $invoice = $addInvoiceForm->getData();
            $path = $addInvoiceForm->get('file')->getData();
            $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);

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

                $invoice->setFile($newFilename)
                    ->setCompany($company);
                $company->addInvoice($invoice);

                $this->em->persist($invoice);
                $this->em->persist($company);

                $this->em->flush();
            }

            return $this->redirectToRoute('admin');
        }

        $companies = $this->companyRepo->findAll();

        return $this->render('invoice/add.html.twig', [
            'add_invoice_form' => $addInvoiceForm->createView(),
            'companies' => $companies
        ]);
    }

    /**
     * @Route("/invoice/add/{company}", name="add_invoice_with_company")
     */
    public function addForCompany(Request $request, Company $company, SluggerInterface $slugger): Response
    {
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceCompanyType::class, $invoice);
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {

            $path = $addInvoiceForm->get('file')->getData();
            $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);

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

                $invoice->setFile($newFilename)
                    ->setCompany($company);
                $company->addInvoice($invoice);

                $this->em->persist($invoice);
                $this->em->persist($company);

                $this->em->flush();
            }

            return $this->redirectToRoute('show_invoices', ['company' => $company->getId()]);
        }

        return $this->render('invoice/add_with_company.html.twig', [
            'company' => $company,
            'add_invoice_form' => $addInvoiceForm->createView()
        ]);
    }

    /**
     * @Route("/invoice/edit/{id}", name="edit_invoice")
     */
    // public function edit(Request $request, Invoice $id): Response
    // {
    //     $updateInvoiceForm = $this->createForm(InvoiceType::class, $id);
    //     $updateInvoiceForm->handleRequest($request);

    //     if ($updateInvoiceForm->isSubmitted() && $updateInvoiceForm->isValid()) {
    //         $this->em->flush();
    //         // return $this->redirectToRoute('show_invoices_user', ['id' => $id->getContract()->getUserId()->getId()]);
    //     }

    //     return $this->render('invoice/edit.html.twig', [
    //         'edit_invoice_form' => $updateInvoiceForm->createView(),
    //         'invoice' => $id
    //     ]);
    // }

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
