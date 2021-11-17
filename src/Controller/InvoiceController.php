<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Form\InvoiceUserType;
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
        $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice, ['method' => 'GET']);
        $addInvoiceForm->handleRequest($request);

        $companies = $this->companyRepo->findAll();

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {

            $path = $addInvoiceForm->get('file')->getData();
            $contract = $addInvoiceForm->get('contract')->getData();
            // $user = $addInvoiceForm->get('user')->getData();

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
                $contract->addInvoice($invoice);
                $this->em->persist($invoice);
                $this->em->persist($contract);

                $this->em->flush();
            }

            // return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('invoice/add.html.twig', [
            'add_invoice_form' => $addInvoiceForm->createView(),
            'companies' => $companies
        ]);
    }

    /**
     * @Route("/invoice/add/{user}", name="add_invoice_user")
     */
    public function addForUser(Request $request, User $user, SluggerInterface $slugger): Response
    {
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceUserType::class, $invoice, array('user' => $user));
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {

            $path = $addInvoiceForm->get('file')->getData();
            $contract = $addInvoiceForm->get('contract')->getData();

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
                var_dump($invoice->getFile());
                $contract->addInvoice($invoice);
                $this->em->persist($invoice);
                $this->em->persist($contract);

                $this->em->flush();
            }

            return $this->redirectToRoute('show_invoices_user', ['id' => $user->getId()]);
        }

        return $this->render('invoice/add_user.html.twig', [
            'user' => $user,
            'add_invoice_form' => $addInvoiceForm->createView()
        ]);
    }

    /**
     * @Route("/invoice/edit/{id}", name="edit_invoice")
     */
    public function edit(Request $request, Invoice $id): Response
    {
        $updateInvoiceForm = $this->createForm(InvoiceType::class, $id);
        $updateInvoiceForm->handleRequest($request);

        if ($updateInvoiceForm->isSubmitted() && $updateInvoiceForm->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('show_invoices_user', ['id' => $id->getContract()->getUserId()->getId()]);
        }

        return $this->render('invoice/edit.html.twig', [
            'edit_invoice_form' => $updateInvoiceForm->createView(),
            'invoice' => $id,
            'user' => $id->getContract()->getUserId()
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
}
