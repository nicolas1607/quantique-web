<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Website;
use App\Entity\Contract;
use App\Entity\TypeContract;
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
     * @Route("/admin/contract/add/modal/{company}", name="add_contract_modal")
     */
    // public function addFromModal(Request $request, Company $company): Response
    // {
    //     $contract = new Contract();
    //     $website = $this->em->getRepository(Website::class)->findOneBy(['name' => $request->get('website')]);

    //     $types = ['vitrine', 'commerce', 'google', 'facebook'];
    //     foreach ($types as $type) {
    //         $check = $request->get($type . '-check');
    //         if ($check == 'on') {
    //             $typeContract = $this->em->getRepository(TypeContract::class)
    //                 ->findOneBy(['lib' => $type]);
    //             $price = $request->get($type . '-price');
    //             $promotion = $request->get($type . '-promotion');
    //             $contract = new Contract;
    //             $contract->setPrice(floatval($price))
    //                 ->setType($typeContract)
    //                 ->setWebsite($website);
    //             if ($promotion && $promotion != $price) {
    //                 $contract->setPromotion(floatval($promotion));
    //             }
    //             $website->addContract($contract);
    //             $this->em->persist($contract);
    //         }
    //     }

    //     $this->em->persist($company);
    //     $this->em->flush();

    //     return $this->redirect($_SERVER['HTTP_REFERER']);
    // }

    /**
     * @Route("/admin/contract/edit/{contract}", name="edit_contract")
     */
    public function edit(Request $request, Contract $contract): Response
    {
        $updateContractForm = $this->createForm(ContractEditType::class, $contract);
        $updateContractForm->handleRequest($request);

        if ($updateContractForm->isSubmitted() && $updateContractForm->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('show_contracts', ['company' => $contract->getWebsite()->getCompany()->getId()]);

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('contract/edit.html.twig', [
            'edit_contract_form' => $updateContractForm->createView(),
            'contract' => $contract,
        ]);
    }

    /**
     * @Route("/admin/contract/delete/{contract}", name="delete_contract")
     */
    public function delete(Contract $contract): Response
    {
        $contract->getWebsite()->removeContract($contract);
        $this->em->remove($contract);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
