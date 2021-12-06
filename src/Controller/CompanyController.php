<?php

namespace App\Controller;

use App\Entity\User;
use FacebookAds\Api;
use Facebook\Facebook;
use App\Entity\Company;
use App\Entity\Website;
use App\Entity\Contract;
use App\Form\CompanyType;
use App\Entity\TypeContract;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\v201809\cm\Paging;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Facebook\Exceptions\FacebookSDKException;
use Google\AdsApi\AdWords\v201809\cm\OrderBy;
use Symfony\Component\HttpFoundation\Request;
use Google\AdsApi\AdWords\v201809\cm\Selector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsClient;

use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Facebook\Exceptions\FacebookResponseException;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Symfony\Component\HttpFoundation\Session\Session;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsClientBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Google\AdsApi\Examples\AdWords\v201809\BasicOperations\GetCampaigns;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/company/show/contracts/{company}", name="show_contracts")
     */
    public function showContracts(Request $request, Company $company, UserPasswordHasherInterface $encoder): Response
    {
        $users = $this->userRepo->findUsers();
        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();

        // Formulaire de modification de mot de passe
        $editPasswordForm = $this->createForm(UserPasswordType::class, $this->getUser());
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isSubmitted() && $editPasswordForm->isValid()) {
            $password = $editPasswordForm->get('password')->getData();
            $passwordEncoded = $encoder->hashPassword($this->getUser(), $password);
            $this->getUser()->setPassword($passwordEncoded);

            $this->em->persist($this->getUser());
            $this->em->flush();
        }

        return $this->render('company/show_contracts.html.twig', [
            'users' => $users,
            'company' => $company,
            'typesContract' => $typesContract,
            'edit_password_form' => $editPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/company/show/invoices/{company}", name="show_invoices")
     */
    public function showInvoices(Request $request, Company $company, UserPasswordHasherInterface $encoder): Response
    {
        // Formulaire de modification de mot de passe
        $editPasswordForm = $this->createForm(UserPasswordType::class, $this->getUser());
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isSubmitted() && $editPasswordForm->isValid()) {
            $password = $editPasswordForm->get('password')->getData();
            $passwordEncoded = $encoder->hashPassword($this->getUser(), $password);
            $this->getUser()->setPassword($passwordEncoded);

            $this->em->persist($this->getUser());
            $this->em->flush();
        }

        return $this->render('company/show_invoices.html.twig', [
            'company' => $company,
            'edit_password_form' => $editPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/company/show/accounts/{company}", name="show_accounts")
     */
    // public function showAccounts(Company $company): Response
    // {
    //     return $this->render('company/show_accounts.html.twig', [
    //         'company' => $company
    //     ]);
    // }

    /**
     * @Route("/company/show/stats/{company}", name="show_stats")
     */
    public function showStats(Company $company): Response
    {
        // $oAuth2Credential = (new OAuth2TokenBuilder())
        //     ->fromFile()
        //     ->build();

        // $session = (new AdWordsSessionBuilder())
        //     ->fromFile()
        //     ->withOAuth2Credential($oAuth2Credential)
        //     ->build();

        // $adWordsServices = new AdWordsServices();

        // $campaignService = $adWordsServices->get($session, CampaignService::class);

        // // Create selector.
        // $selector = new Selector();
        // $selector->setFields(array('Id', 'Name'));
        // $selector->setOrdering(array(new OrderBy('Name', 'ASCENDING')));

        // // Create paging controls.
        // $selector->setPaging(new Paging(0, 100));

        // // Make the get request.
        // $page = $campaignService->get($selector);





        // $campaigns = new GetCampaigns();
        // $campaigns->runExample($adWordsServices, $session);







        // FACEBOOK & INSTAGRAM //

        // $fb = new Facebook([
        //     'app_id' => '603614854173588',
        //     'app_secret' => 'f47a275c069c8759904cfca950d8a8ee',
        // ]);

        // try {
        //     // Returns a `Facebook\FacebookResponse` object
        //     $response = $fb->get('/4932955780050365', 'EAAIkZCAj205QBAJzADpssZBZCnFjiGvDgZAojV22iK2jNbjp5CXpyIo8wN8VDpMyWQei4NUlhD1G3aZCBAKyTbDnaWFeFovXNdxLmrFKiaqcYcuoVJmQQab0RhIj2cuOcWRuKQKAS07ZARvV2ZCF0kkfCkTFZBYJyB1ZBxwFBTAgLBGPi8JUe0A5UNLT2L1slUNtYUAGUW1UcgMZCLAlFq500s');
        // } catch (FacebookResponseException $e) {
        //     echo 'Graph returned an error: ' . $e->getMessage();
        //     exit;
        // } catch (FacebookSDKException $e) {
        //     echo 'Facebook SDK returned an error: ' . $e->getMessage();
        //     exit;
        // }
        // $graphNode = $response->getGraphNode();
        // var_dump($graphNode);

        return $this->render('company/show_stats.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("/admin/company/add", name="add_company")
     */
    public function add(Request $request): Response
    {
        $company = new Company();
        $addCompanyForm = $this->createForm(CompanyType::class, $company);
        $addCompanyForm->handleRequest($request);

        if ($addCompanyForm->isSubmitted() && $addCompanyForm->isValid()) {
            $company = $addCompanyForm->getData();

            // Abonnement
            $website = null;
            if ($request->get('website') != '' && $request->get('url') != '') {
                $website = new Website();
                $website->setName($request->get('website'))
                    ->setUrl($request->get('url'))
                    ->setCompany($company);
                $company->addWebsite($website);
                $this->em->persist($website);
            }

            // Liste des contrats ajoutés
            if ($website != null) {
                $types = ['vitrine', 'commerce', 'google', 'facebook'];
                foreach ($types as $type) {
                    $check = $request->get($type . '-check');
                    if ($check == 'on') {
                        $typeContract = $this->em->getRepository(TypeContract::class)
                            ->findOneBy(['lib' => $type]);
                        $price = $request->get($type . '-price');
                        $promotion = $request->get($type . '-promotion');
                        $contract = new Contract;
                        $contract->setPrice(floatval($price))
                            ->setType($typeContract)
                            ->setWebsite($website);
                        if ($promotion && $promotion != $price) {
                            $contract->setPromotion(floatval($promotion));
                        }
                        $website->addContract($contract);
                        $this->em->persist($contract);
                    }
                }
            }


            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('admin_companies');
        }

        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();

        return $this->render('company/add_all.html.twig', [
            'add_company_form' => $addCompanyForm->createView(),
            'types' => $typesContract
        ]);
    }

    /**
     * @Route("/admin/company/add/{user}", name="add_company_user")
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
     * @Route("/admin/company/edit/{company}", name="edit_company")
     */
    public function edit(Request $request, Company $company): Response
    {
        $company->setName($request->get('name'))
            ->setEmail($request->get('email'))
            ->setPhone($request->get('phone'))
            ->setAddress($request->get('address'))
            ->setPostalCode($request->get('postalCode'))
            ->setCity($request->get('city'))
            ->setNumTVA($request->get('numTVA'))
            ->setSiret($request->get('siret'));
        $this->em->persist($company);
        $this->em->flush();

        return $this->redirectToRoute('admin_companies');
    }

    /**
     * @Route("/admin/company/delete/{company}", name="delete_company")
     */
    public function delete(Company $company): Response
    {
        $this->em->remove($company);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
