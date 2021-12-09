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
use App\Entity\GoogleAccount;
use App\Entity\FacebookAccount;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $em;
    private CompanyRepository $companyRepo;
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, CompanyRepository $companyRepo, UserRepository $userRepo)
    {
        $this->em = $em;
        $this->companyRepo = $companyRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/admin/companies", name="admin_companies")
     */
    public function companies(Request $request, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        $search = $request->get('search');
        if ($search != null) {
            $companies = $this->companyRepo->findSearch($search);
        } else {
            $companies = $this->em->getRepository(Company::class)->findAll();
        }

        $users = $this->userRepo->findUsers();
        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();
        $typesInvoice = $this->em->getRepository(TypeInvoice::class)->findAll();
        $currentDate = new DateTime();

        // Formulaire d'ajout de facture
        $invoice = new Invoice();
        $addInvoiceForm = $this->createForm(InvoiceType::class, $invoice);
        $addInvoiceForm->handleRequest($request);

        if ($addInvoiceForm->isSubmitted() && $addInvoiceForm->isValid()) {

            $company = $this->em->getRepository(Company::class)->findOneBy(['name' => $request->get('company')]);

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
                    ->subject('Nouvelle(s) facture(s) pour ' . $company->getName() . ' disponible(s) !')
                    ->htmlTemplate('emails/invoice_confirmation.html.twig')
                    ->context([
                        'user' => $user,
                        'company' => $company
                    ]);
                foreach ($invoices as $invoice) {
                    $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());
                }

                $mailer->send($email);
            }
        }



        return $this->render('admin/companies.html.twig', [
            'users' => $users,
            'companies' => $companies,
            'search' => $search,
            'typesContract' => $typesContract,
            'typesInvoice' => $typesInvoice,
            'currentDate' => $currentDate,
            'add_invoice_form' => $addInvoiceForm->createView()
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function users(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $search = $request->get('search');
        if ($search != null) {
            $users = $this->userRepo->findSearch($search);
        } else {
            $users = $this->em->getRepository(User::class)->findAll();
        }
        $companies = $this->em->getRepository(Company::class)->findAll();

        // Formulaire de modification de mot de passe
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $request->get('ident')]);
        $editPasswordForm = $this->createForm(UserPasswordType::class, $user);
        $editPasswordForm->handleRequest($request);

        if ($editPasswordForm->isSubmitted() && $editPasswordForm->isValid()) {
            $password = $editPasswordForm->get('password')->getData();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'search' => $search,
            'companies' => $companies,
            'edit_password_form' => $editPasswordForm->createView()
        ]);
    }

    /**
     * @Route("/admin/notes", name="admin_notes")
     */
    public function notes(Request $request): Response
    {
        $search = $request->get('search');
        if ($search != null) {
            $notes = $this->noteRepo->findSearch($search);
        } else {
            $notes = $this->em->getRepository(Note::class)->findAll();
        }
        return $this->render('admin/notes.html.twig', [
            'notes' => $notes,
            'search' => $search
        ]);
    }

    /**
     * @Route("/admin/user/add/modal", name="add_user_modal")
     */
    public function addFromModal(Request $request, UserPasswordHasherInterface $encoder, MailerInterface $mailer): Response
    {
        $user = new User;
        $userexist = new User;
        $userExist = $request->get('user');
        if ($userExist) {
            $userexist = $this->em->getRepository(User::class)->findOneBy([
                'id' => $userExist
            ]);
        }
        if ($request->get('firstname') && $request->get('lastname') && $request->get('email')) {
            $user->setFirstname($request->get('firstname'))
                ->setLastname($request->get('lastname'))
                ->setEmail($request->get('email'))
                ->setPhone($request->get('phone'));
        }

        $company = $this->em->getRepository(Company::class)->findOneBy(['name' => $request->get('company')]);
        if ($userexist->getEmail()) {
            $userexist->addCompany($company);
            $company->addUser($userexist);
            $this->em->persist($userexist);
        }
        if ($user->getEmail()) {
            $user->addCompany($company);
            $company->addUser($user);
            $this->em->persist($user);
        }

        $password = 'quantique';
        $passwordEncoded = $encoder->hashPassword($user, $password);
        $user->setPassword($passwordEncoded);

        $this->em->persist($company);
        $this->em->flush();

        // mail de confirmation
        if ($user->getId()) {
            $email = (new TemplatedEmail())
                ->from('noreply@quantique-web.fr')
                ->to($user->getEmail())
                ->subject('Accédez à votre compte Quantique Web Office !')
                ->htmlTemplate('emails/user_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'password' => 'quantique'
                ]);

            $mailer->send($email);
        }
        if ($userexist->getId()) {
            $email = (new TemplatedEmail())
                ->from('noreply@quantique-web.fr')
                ->to($userexist->getEmail())
                ->subject('Accédez à votre compte Quantique Web Office !')
                ->htmlTemplate('emails/user_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'password' => 'quantique'
                ]);

            $mailer->send($email);
        }

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/profile/user/edit/modal/{user}", name="edit_user_modal")
     */
    public function edit(Request $request, User $user): Response
    {
        // On créer les comptes Google & Facebook
        // $emailGoogle = $request->query->get('emailGoogle');
        // $mdpGoogle = $request->query->get('mdpGoogle');
        // if ($emailGoogle && $mdpGoogle) {
        //     $accountGoogle = new GoogleAccount();
        //     $accountGoogle->setEmail($emailGoogle)
        //         ->setPassword($encoder->hashPassword($user, $mdpGoogle));
        //     $user->setGoogleAccount($accountGoogle);
        //     $this->em->persist($accountGoogle);
        // }
        // $emailFb = $request->query->get('emailFb');
        // $mdpFb = $request->query->get('mdpFb');
        // if ($emailFb && $mdpFb) {
        //     $accountFb = new FacebookAccount();
        //     $accountFb->setEmail($emailFb)
        //         ->setPassword($encoder->hashPassword($user, $mdpFb));
        //     $user->setGoogleFacebook($accountFb);
        //     $this->em->persist($accountFb);
        // }

        $user->setFirstname($request->get('firstname'))
            ->setLastname($request->get('lastname'))
            ->setEmail($request->get('email'))
            ->setPhone($request->get('phone'));

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/profile/user/edit/password/{user}", name="edit_user_password")
     */
    public function editPassword(Request $request, User $user, UserPasswordHasherInterface $encoder): Response
    {
        $editUserForm = $this->createForm(UserPasswordType::class, $user);

        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $user = $editUserForm->getData();

            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $user->setNbConnection($user->getNbConnection() + 1);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts', ['company' => $this->getUser()->getCompanies()[0]->getId()]);
        }

        return $this->render('user/edit_password.html.twig', [
            'user' => $user,
            'edit_user_form' => $editUserForm->createView()
        ]);
    }

    /**
     * @Route("/admin/user/delete/{user}", name="delete_user")
     */
    public function delete(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
