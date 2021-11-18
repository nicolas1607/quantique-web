<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
{
    private EntityManagerInterface $em;
    private CompanyRepository $companyRepo;

    public function __construct(EntityManagerInterface $em, CompanyRepository $companyRepo)
    {
        $this->em = $em;
        $this->companyRepo = $companyRepo;
    }

    /**
     * @Route("/company/add/{user}", name="add_company")
     */
    public function add(Request $request, User $user): Response
    {
        $company = new Company();
        $addCompanyForm = $this->createForm(CompanyType::class, $company);
        $addCompanyForm->handleRequest($request);

        if ($addCompanyForm->isSubmitted() && $addCompanyForm->isValid()) {
            $company = $addCompanyForm->getData();
            $company->addUser($user);
            $user->addCompany($company);

            $this->em->persist($company);
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('company/add.html.twig', [
            'add_company_form' => $addCompanyForm->createView()
        ]);
    }

    /**
     * @Route("/company/edit/{company}", name="edit_company")
     */
    public function edit(Request $request, Company $company): Response
    {
        $updateContractForm = $this->createForm(CompanyType::class, $company);
        $updateContractForm->handleRequest($request);

        if ($updateContractForm->isSubmitted() && $updateContractForm->isValid()) {
            $this->em->flush();
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        return $this->render('company/edit.html.twig', [
            'edit_company_form' => $updateContractForm->createView(),
            'company' => $company,
        ]);
    }

    /**
     * @Route("/company/show/{company}", name="show_company")
     */
    public function show(Request $request, Company $company): Response
    {
        $nbContract = $this->companyRepo->findNbContract($company);
        return $this->render('company/show.html.twig', [
            'company' => $company,
            'nbContract' => $nbContract
        ]);
    }
}
