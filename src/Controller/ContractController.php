<?php

namespace App\Controller;

use App\Entity\Contract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContractController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/contract/delete/{contract}", name="delete_contract")
     */
    public function delete(Contract $contract): Response
    {
        $contract->getWebsite()->removeContract($contract);
        $this->em->remove($contract);
        $this->em->flush();

        $this->addFlash(
            'success',
            $contract->getWebsite() . ' : contrat ' . $contract->getType()->getName() . ' supprimé avec succès !'
        );

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
