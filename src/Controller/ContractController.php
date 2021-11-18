<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Website;
use App\Entity\Contract;
use App\Form\ContractType;
use App\Entity\TypeContract;
use App\Form\ContractInfoType;
use App\Form\ContractUserType;
use Doctrine\ORM\EntityManager;
use App\Repository\CompanyRepository;
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
     * @Route("/contract/add", name="add_contract")
     */
    public function add(Request $request): Response
    {
        $contract = new Contract();
        $addContractForm = $this->createForm(ContractType::class, $contract);
        $addContractForm->handleRequest($request);

        if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
            $contract = $addContractForm->getData();
            $website = $contract->getWebsite();
            $website->addContract($contract);

            $this->em->persist($contract);
            $this->em->persist($website);
            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('contract/add.html.twig', [
            'add_contract_form' => $addContractForm->createView()
        ]);
    }

    /**
     * @Route("/contract/add/{website}/{type}", name="add_contract_with_type")
     */
    public function addWithType(Request $request, Website $website, TypeContract $type): Response
    {
        $contract = new Contract();
        $addContractForm = $this->createForm(ContractInfoType::class, $contract);
        $addContractForm->handleRequest($request);

        if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
            $contract = $addContractForm->getData();
            $website = $contract->getWebsite();
            $website->addContract($contract);

            $this->em->persist($contract);
            $this->em->persist($website);
            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('contract/add_type.html.twig', [
            'add_contract_form' => $addContractForm->createView(),
            'website' => $website,
            'type' => $type
        ]);
    }

    /**
     * @Route("/contract/add/{user}", name="add_contract_user")
     */
    // public function addForUser(Request $request, User $user): Response
    // {
    //     $contract = new Contract();
    //     $addContractForm = $this->createForm(ContractUserType::class, $contract);
    //     $addContractForm->handleRequest($request);

    //     if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
    //         $contract = $addContractForm->getData();
    //         $company = $addContractForm->getData('company');
    //         $company->addContract($contract);

    //         $this->em->persist($contract);
    //         $this->em->persist($company);
    //         $this->em->flush();

    //         // return $this->redirectToRoute('show_contracts_user', ['id' => $user->getId()]);
    //         return $this->redirect($_SERVER['HTTP_REFERER']);
    //     }

    //     return $this->render('contract/add_user.html.twig', [
    //         'user' => $user,
    //         'add_contract_form' => $addContractForm->createView()
    //     ]);
    // }

    /**
     * @Route("/contract/edit/{id}", name="edit_contract")
     */
    public function edit(Request $request, Contract $id): Response
    {
        $updateContractForm = $this->createForm(ContractType::class, $id);
        $updateContractForm->handleRequest($request);

        if ($updateContractForm->isSubmitted() && $updateContractForm->isValid()) {
            $this->em->flush();
            // return $this->redirectToRoute('show_contracts_user', ['id' => $id->getCompany()->getUsers()->getId()]);
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('contract/edit.html.twig', [
            'edit_contract_form' => $updateContractForm->createView(),
            'contract' => $id,
        ]);
    }
}
