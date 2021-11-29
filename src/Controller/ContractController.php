<?php

namespace App\Controller;

use App\Entity\Website;
use App\Entity\Contract;
use App\Form\ContractAddType;
use App\Form\ContractEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/contract/add/{website}/{type}", name="add_contract_with_type")
     */
    // public function addWithType(Request $request, Website $website, TypeContract $type): Response
    // {
    //     $contract = new Contract();
    //     $addContractForm = $this->createForm(ContractInfoType::class, $contract);
    //     $addContractForm->handleRequest($request);

    //     if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
    //         $contract = $addContractForm->getData();
    //         $contract->setWebsite($website)
    //             ->setType($type);
    //         $website->addContract($contract);

    //         $this->em->persist($contract);
    //         $this->em->persist($website);
    //         $this->em->flush();

    //         return $this->redirectToRoute('show_website', ['website' => $website->getId()]);
    //     }

    //     return $this->render('contract/add_type.html.twig', [
    //         'add_contract_form' => $addContractForm->createView(),
    //         'website' => $website,
    //         'type' => $type
    //     ]);
    // }

    /**
     * @Route("/contract/add/{website}", name="add_contract_with_website")
     */
    public function addWithWebsite(Request $request, Website $website): Response
    {
        $contract = new Contract();
        $addContractForm = $this->createForm(ContractAddType::class, $contract);
        $addContractForm->handleRequest($request);

        if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
            $contract = $addContractForm->getData();
            $contract->setWebsite($website);
            $website->addContract($contract);

            $this->em->persist($contract);
            $this->em->persist($website);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts', ['company' => $website->getCompany()->getId()]);
        }

        return $this->render('contract/add_with_website.html.twig', [
            'add_contract_form' => $addContractForm->createView(),
            'website' => $website
        ]);
    }

    /**
     * @Route("/contract/edit/{contract}", name="edit_contract")
     */
    public function edit(Request $request, Contract $contract): Response
    {
        $updateContractForm = $this->createForm(ContractEditType::class, $contract);
        $updateContractForm->handleRequest($request);

        if ($updateContractForm->isSubmitted() && $updateContractForm->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('show_website', ['website' => $contract->getWebsite()->getId()]);
        }

        return $this->render('contract/edit.html.twig', [
            'edit_contract_form' => $updateContractForm->createView(),
            'contract' => $contract,
        ]);
    }

    /**
     * @Route("/contract/delete/{contract}", name="delete_contract")
     */
    public function delete(Contract $contract): Response
    {
        $contract->getWebsite()->removeContract($contract);
        $this->em->remove($contract);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
