<?php

namespace App\Controller;

use DateTime;
use App\Entity\Invoice;
use App\Entity\TypeInvoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/invoice/edit/{id}", name="edit_invoice")
     */
    public function edit(Request $request, Invoice $id): Response
    {
        $id->setType($this->em->getRepository(TypeInvoice::class)
            ->findOneBy(['name' => $request->get('type')]))
            ->setReleasedAt(new DateTime($request->get('date')));
        $this->em->persist($id);
        $this->em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/profile/invoice/download/{id}", name="download_invoice")
     */
    public function download(Invoice $id): Response
    {
        $pdfPath = $this->getParameter('invoices') . '/' . $id->getFile();
        return $this->file($pdfPath);
    }

    /**
     * @Route("/admin/invoice/delete/{invoice}", name="delete_invoice")
     */
    public function delete(Invoice $invoice): Response
    {
        $invoice->getCompany()->removeInvoice($invoice);
        $this->em->remove($invoice);
        $this->em->flush();

        $this->addFlash('success', 'Facture supprimée avec succès !');

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
