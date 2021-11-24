<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Website;
use App\Entity\Contract;
use App\Form\CompanyType;
use App\Entity\TypeContract;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Google\AdsApi\Examples\AdWords\v201809\BasicOperations\GetCampaigns;

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
     * @Route("/company/show/contracts/{company}", name="show_contracts")
     */
    public function showContracts(Company $company): Response
    {
        return $this->render('company/show_contracts.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("/company/show/invoices/{company}", name="show_invoices")
     */
    public function showInvoices(Company $company): Response
    {
        return $this->render('company/show_invoices.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("/company/show/accounts/{company}", name="show_accounts")
     */
    public function showAccounts(Company $company): Response
    {
        return $this->render('company/show_accounts.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("/company/show/stats/{company}", name="show_stats")
     */
    public function showStats(Company $company): Response
    {
        return $this->render('company/show_stats.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("/company/add", name="add_company")
     */
    public function add(Request $request): Response
    {
        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();

        $company = new Company();
        $addCompanyForm = $this->createForm(CompanyType::class, $company);
        $addCompanyForm->handleRequest($request);

        if ($addCompanyForm->isSubmitted() && $addCompanyForm->isValid()) {
            $company = $addCompanyForm->getData();

            // Abonnement
            if ($request->get('website') != '' && $request->get('url') != '') {
                $website = new Website();
                $website->setName($request->get('website'))
                    ->setUrl($request->get('url'))
                    ->setCompany($company);
                $company->addWebsite($website);
                $this->em->persist($website);
            }

            // Liste des contrats ajoutés
            $types = ['vitrine', 'commerce', 'google', 'facebook'];
            foreach ($types as $type) {
                $check = $request->get($type . '-check');
                if ($check == 'on') {
                    $typeContract = $this->em->getRepository(TypeContract::class)
                        ->findOneBy(['lib' => $type]);
                    $price = $request->get($type . '-price');
                    $promotion = $request->get($type . '-promotion');
                    $contract = new Contract;
                    $contract->setPrice($price)
                        ->setPromotion($promotion)
                        ->setType($typeContract)
                        ->setWebsite($website);
                    $website->addContract($contract);
                    $this->em->persist($contract);
                }
            }


            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('company/add_all.html.twig', [
            'add_company_form' => $addCompanyForm->createView(),
            'types' => $typesContract
        ]);
    }

    /**
     * @Route("/company/add/{user}", name="add_company_user")
     */
    // public function addForUser(Request $request, User $user): Response
    // {
    //     $company = new Company();
    //     $addCompanyForm = $this->createForm(CompanyType::class, $company);
    //     $addCompanyForm->handleRequest($request);

    //     if ($addCompanyForm->isSubmitted() && $addCompanyForm->isValid()) {
    //         $company = $addCompanyForm->getData();
    //         $company->addUser($user);
    //         $user->addCompany($company);

    //         $this->em->persist($company);
    //         $this->em->persist($user);
    //         $this->em->flush();

    //         return $this->redirectToRoute('admin_users');
    //     }

    //     return $this->render('company/add_user.html.twig', [
    //         'add_company_form' => $addCompanyForm->createView(),
    //         'user' => $user
    //     ]);
    // }

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
     * @Route("/company/delete/{company}", name="delete_company")
     */
    public function delete(Company $company): Response
    {
        $this->em->remove($company);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
