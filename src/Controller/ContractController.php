<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contract;
use App\Form\ContractType;
use Doctrine\ORM\EntityManager;
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
     * @Route("/contract/add/{user}", name="add_contract")
     */
    public function add(Request $request, User $user): Response
    {
        $contract = new Contract();
        $addContractForm = $this->createForm(ContractType::class, $contract);
        $addContractForm->handleRequest($request);

        if ($addContractForm->isSubmitted() && $addContractForm->isValid()) {
            $contract->setUserId($user);
            $user->addContract($contract);
            $this->em->persist($contract);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts_user', ['id' => $user->getId()]);
        }

        return $this->render('contract/add.html.twig', [
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
            return $this->redirectToRoute('show_contract', ['id' => $id->getId()]);
        }

        return $this->render('contract/edit.html.twig', [
            'edit_contract_form' => $updateContractForm->createView()
        ]);
    }

    /**
     * @Route("/contract/delete/{id}", name="delete_contract")
     */
    // public function delete(Request $request, Contract $id): Response
    // {
    //     $this->em->remove($id);
    //     $this->em->flush();

    //     return $this->redirect($request->headers->get('referer'));
    // }
}
