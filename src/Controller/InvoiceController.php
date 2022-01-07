<?php

namespace App\Controller;

use ZipArchive;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/profile/invoice/download/{id}", name="download_invoice")
     */
    public function download(Invoice $id): Response
    {
        $pdfPath = $this->getParameter('invoices') . '/' . $id->getFile();
        return $this->file($pdfPath);
    }

    /**
     * Create and download some zip documents.
     *
     * @param array $documents
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function zipDownloadDocumentsAction(array $documents)
    {
        $files = [];
        $em = $this->getDoctrine()->getManager();

        foreach ($documents as $document) {
            array_push($files, '../web/' . $document->getWebPath());
        }

        // Create new Zip Archive.
        $zip = new \ZipArchive();

        // The name of the Zip documents.
        $zipName = 'Documents.zip';

        $zip->open($zipName,  \ZipArchive::CREATE);
        foreach ($files as $file) {
            $zip->addFromString(basename($file),  file_get_contents($file));
        }
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        @unlink($zipName);

        return $response;
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
