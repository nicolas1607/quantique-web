<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contract;
use App\Form\ContractType;
use App\Form\ContractUserType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContractController extends AbstractController
{
    private EntityManagerInterface $em;
    private CompanyRepository $companyRepo;

    public function __construct(EntityManagerInterface $em, CompanyRepository $companyRepo)
    {
        $this->em = $em;
        $this->companyRepo = $companyRepo;
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
            $company = $contract->getCompany();
            $company->addContract($contract);

            $this->em->persist($contract);
            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('contract/add.html.twig', [
            // 'user' => $user,
            'add_contract_form' => $addContractForm->createView()
        ]);
    }

    /**
     * @Route("/contract/add/{user}", name="add_contract_user")
     */
    public function addForUser(Request $request, User $user): Response
    {
        $contract = new Contract();
        $addContractForm = $this->createForm(ContractUserType::class, $contract);
        $addContractForm->handleRequest($request);

        if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
            $contract = $addContractForm->getData();
            $company = $addContractForm->getData('company');
            $company->addContract($contract);

            $this->em->persist($contract);
            $this->em->persist($company);
            $this->em->flush();

            // return $this->redirectToRoute('show_contracts_user', ['id' => $user->getId()]);
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('contract/add_user.html.twig', [
            'user' => $user,
            'add_contract_form' => $addContractForm->createView()
        ]);
    }

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
