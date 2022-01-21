<?php

namespace App\Controller;

use DateTime;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Entity\TypeInvoice;
use App\Entity\TypeContract;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Google\Ads\GoogleAds\Util\V9\ResourceNames;
use Symfony\Component\Routing\Annotation\Route;
use Google\Ads\GoogleAds\V9\Resources\BillingSetup;
use Symfony\Component\String\Slugger\SluggerInterface;
use Google\Ads\GoogleAds\Lib\V7\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V9\Enums\MonthOfYearEnum\MonthOfYear;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepo;
    private InvoiceRepository $invoiceRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, InvoiceRepository $invoiceRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->invoiceRepo = $invoiceRepo;
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

            $this->addFlash('success', 'Mot de passe modifié avec succès !');
        }

        // GET MAX ID NOTE
        $id = 0;
        $notes = $this->em->getRepository(Note::class)->findAll();
        foreach ($notes as $note) {
            if ($note->getId() > $id) {
                $id = $note->getId();
            }
        }

        return $this->render('company/show_contracts.html.twig', [
            'users' => $users,
            'company' => $company,
            'typesContract' => $typesContract,
            'noteId' => $id,
            'edit_password_form' => $editPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/company/show/invoices/{company}", name="show_invoices")
     */
    public function showInvoices(Request $request, Company $company, UserPasswordHasherInterface $encoder, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        if ($request->get('all-download') == 'on') {
            $action = 'download';
        } else if ($request->get('all-delete') == 'on') {
            $action = 'delete';
        } else {
            $action = null;
        }
        // Les factures qui ont été cochées
        $res = [];
        $invoicesAll = $this->em->getRepository(Invoice::class)->findBy(['company' => $company->getId()]);
        foreach ($invoicesAll as $inv) {
            $check = $request->get('invoiceCheck' . $inv->getId());
            if ($check == 'on') {
                $res[] = $this->em->getRepository(Invoice::class)->findOneBy(['id' => $inv->getId()]);
            }
        }
        // Download or Delete
        if ($action != null && count($res) == 0) {
            $this->addFlash('alert', 'Veuillez sélectionner une ou plusieurs factures !');
        } else {
            if ($action == 'download') {
                $zip = new \ZipArchive();
                $zipName = 'quantique_web_factures.zip';

                $zip->open($zipName,  \ZipArchive::CREATE);
                foreach ($res as $file) {
                    $zip->addFromString(basename($file->getFile()), file_get_contents($this->getParameter('invoices') . '/' . $file->getFile()));
                }
                $zip->close();

                $response = new Response(file_get_contents($zipName));
                $response->headers->set('Content-Type', 'application/zip');
                $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
                $response->headers->set('Content-length', filesize($zipName));
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter('zip'));
                return $response;
            } else if ($action == 'delete') {
                foreach ($res as $file) {
                    $this->em->remove($file);
                }
                $this->em->flush();
                $this->addFlash('success', 'Factures supprimées avec succès !');
            }
        }

        // Formulaire de modification de mot de passe
        $editPasswordForm = $this->createForm(UserPasswordType::class, $this->getUser());
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isSubmitted() && $editPasswordForm->isValid()) {
            $password = $editPasswordForm->get('password')->getData();
            $passwordEncoded = $encoder->hashPassword($this->getUser(), $password);
            $this->getUser()->setPassword($passwordEncoded);
            $this->em->persist($this->getUser());
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe modifié avec succès !');
        }

        // Formulaire d'ajout de facture
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice);
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {
            $company = $request->get('company');

            // Fichier PDF *
            $paths = $addInvoiceForm->get('files')->getData();
            foreach ($paths as $path) {
                $invoice = new Invoice();
                $invoice->setReleasedAt(new DateTime($request->get('date')))
                    ->setType($this->em->getRepository(TypeInvoice::class)
                        ->findOneBy(['name' => $request->get('type')]))
                    ->setCompany($company);
                if ($path) {
                    $originalFilename = pathinfo($path->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '.' . $path->guessExtension();
                    try {
                        $path->move(
                            $this->getParameter('invoices'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $invoice->setFile($newFilename);
                    $company->addInvoice($invoice);
                    $this->em->persist($invoice);
                    $this->em->persist($company);
                    $this->em->flush();
                    $invoices[] = $invoice;
                }
            }

            // Envoie d'un mail de confirmation
            foreach ($company->getUsers() as $user) {
                $email = (new TemplatedEmail())
                    ->from('noreply@quantique-web.fr')
                    ->to($user->getEmail())
                    ->subject('Nouvelle(s) facture(s) Quantique Web disponible(s) !')
                    ->htmlTemplate('emails/invoice_confirmation.html.twig')
                    ->context([
                        'invoices' => $invoices,
                        'user' => $user,
                        'company' => $company
                    ]);
                foreach ($invoices as $invoice) {
                    $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());
                }
                $mailer->send($email);
            }
            $this->addFlash('success', 'Facture(s) ajoutée(s) à ' . $company->getName() . ' !');
            return $this->redirect($request->getUri());
        }


        $currentDate = new DateTime();
        $typesInvoice = $this->em->getRepository(TypeInvoice::class)->findAll();
        $users = $this->userRepo->findUsers();

        $invoices = $this->invoiceRepo->findOrderByDate($company);

        return $this->render('company/show_invoices.html.twig', [
            'users' => $users,
            'company' => $company,
            'invoices' => $invoices,
            'typesInvoice' => $typesInvoice,
            'currentDate' => $currentDate,
            'edit_password_form' => $editPasswordForm->createView(),
            'add_invoice_form' => $addInvoiceForm->createView()
        ]);
    }

    /**
     * @Route("/company/show/stats/{company}", name="show_stats")
     */
    public function showStats(Company $company): Response
    {
        // OAuth2 Identification
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile('/Users/nicolasmormiche/google_ads_php.ini')
            ->build();

        // GoogleAds Identification
        $googleAdsClient = (new GoogleAdsClientBuilder())
            ->withOAuth2Credential($oAuth2Credential)
            ->fromFile('/Users/nicolasmormiche/google_ads_php.ini')
            ->build();

        $customerId = '161-244-5303';
        $billingSetupId = '5911-5010-7531';

        // Gets the date one month before now.
        $lastMonth = strtotime('-1 month');

        // Issues the request.
        $response = $googleAdsClient->getInvoiceServiceClient()->listInvoices(
            $customerId,
            ResourceNames::forBillingSetup($customerId, $billingSetupId),
            // The year needs to be 2019 or later.
            date('Y', $lastMonth),
            MonthOfYear::value(strtoupper(date('F', $lastMonth)))
        );



        // FACEBOOK & INSTAGRAM //

        // $app_id = "662888525073742";
        // $app_secret = "e5e6e86cbe6211adc4b619ba0630e529";
        // $access_token = "d46ee756cba654293fc67b0c7a3084d0";
        // $account_id = "934306636983189"; // nico 228753810	

        // $fb = new Facebook([
        //     'app_id' => '662888525073742',
        //     'app_secret' => 'e5e6e86cbe6211adc4b619ba0630e529',
        // ]);

        // try {
        //     // Returns a `Facebook\FacebookResponse` object
        //     $response = $fb->get(
        //         '/934306636983189/insights?fields=cost_per_store_visit_action%2Cstore_visit_actions',
        //         'd46ee756cba654293fc67b0c7a3084d0'
        //     );
        // } catch (FacebookResponseException $e) {
        //     echo 'Graph returned an error: ' . $e->getMessage();
        //     exit;
        // } catch (FacebookSDKException $e) {
        //     echo 'Facebook SDK returned an error: ' . $e->getMessage();
        //     exit;
        // }
        // $graphNode = $response->getGraphNode();
        // 
        _dump($graphNode);

        // $res = $this->useCurl(
        //     'https://graph.facebook.com/v12.0/228753810/insights',
        //     [
        //         'fields:        cost_per_store_visit_action,store_visit_actions',
        //         'access_token:  d46ee756cba654293fc67b0c7a3084d0'
        //     ]
        // );

        $users = $this->userRepo->findUsers();

        return $this->render('company/show_stats.html.twig', [
            'company' => $company,
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/company/edit/{company}", name="edit_company")
     */
    public function edit(Request $request, Company $company): Response
    {
        $email = $request->get('email');
        // verif si email valide
        if (!preg_match("^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^", $email)) {
            $this->addFlash('alert', 'Veuillez saisir une adresse email valide !');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
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

            $this->addFlash('success', $company->getName() . ' modifiée avec succès !');
        }

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/admin/user/remove/{company}/{user}", name="remove_user")
     */
    public function removeUser(Company $company, User $user): Response
    {
        $user->removeCompany($company);
        $company->removeUser($user);
        $this->em->persist($user);
        $this->em->persist($company);
        $this->em->flush();

        $this->addFlash(
            'success',
            $user->getFirstname() . ' ' . $user->getLastname() . ' supprimé(e) avec succès de ' . $company->getName() . ' !'
        );

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/admin/company/delete/{company}", name="delete_company")
     */
    public function delete(Company $company): Response
    {
        $this->em->remove($company);
        $this->em->flush();

        $this->addFlash('success', $company->getName() . ' supprimée avec succès !');

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
