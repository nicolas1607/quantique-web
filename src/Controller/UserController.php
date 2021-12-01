<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\User;
use App\Entity\Company;
use App\Form\UserAddType;
use App\Form\UserEditType;
use App\Entity\GoogleAccount;
use App\Form\UserPasswordType;
use App\Entity\FacebookAccount;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function companies(Request $request): Response
    {
        $search = $request->get('search');
        if ($search != null) {
            $companies = $this->companyRepo->findSearch($search);
        } else {
            $companies = $this->em->getRepository(Company::class)->findAll();
        }
        return $this->render('admin/companies.html.twig', [
            'companies' => $companies,
            'search' => $search
        ]);
    }

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function users(Request $request): Response
    {
        $search = $request->get('search');
        if ($search != null) {
            $users = $this->userRepo->findSearch($search);
        } else {
            $users = $this->em->getRepository(User::class)->findAll();
        }
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'search' => $search
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

    // GENERAL //

    /**
     * @Route("/add/user", name="add_user")
     */
    public function add(Request $request, UserPasswordHasherInterface $encoder, MailerInterface $mailer): Response
    {
        $user = new User();
        $addUserForm = $this->createForm(UserAddType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();
            $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);
            $company->addUser($user);
            $user->addCompany($company);

            $password = 'Quantique2021-';
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->persist($company);
            $this->em->flush();

            // mail de confirmation
            $email = (new TemplatedEmail())
                ->from('nicolas160796@gmail.com')
                ->to($user->getEmail())
                ->subject('Accédez à votre compte Quantique Web Office !')
                ->htmlTemplate('emails/user_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'password' => 'Quantique2021-'
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('admin_users');
        }

        $companies = $this->em->getRepository(Company::class)->findAll();

        return $this->render('user/add.html.twig', [
            'add_user_form' => $addUserForm->createView(),
            'companies' => $companies
        ]);
    }

    /**
     * @Route("/add/user/{company}", name="add_user_with_company")
     */
    public function addForCompany(Request $request, Company $company, MailerInterface $mailer, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $addUserForm = $this->createForm(RegistrationFormType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();
            $user->addCompany($company);
            $company->addUser($user);

            $password = 'Quantique2021-';
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->persist($company);
            $this->em->flush();

            // mail de confirmation
            $email = (new TemplatedEmail())
                ->from('nicolas160796@gmail.com')
                ->to($user->getEmail())
                ->subject('Accédez à votre compte Quantique Web Office !')
                ->htmlTemplate('emails/user_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'password' => 'Quantique2021-'
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('admin_users');

            // return $this->redirectToRoute('email_user_confirmation', [
            //     'user' => $user->getId(),
            //     'password' => $password
            // ]);
            // return $this->redirectToRoute('show_contracts', ['company' => $company->getId()]);
        }

        return $this->render('user/add_with_company.html.twig', [
            'add_user_form' => $addUserForm->createView(),
            'company' => $company
        ]);
    }

    /**
     * @Route("/user/edit/{user}", name="edit_user")
     */
    public function edit(Request $request, User $user, UserPasswordHasherInterface $encoder): Response
    {
        $editUserForm = $this->createForm(UserEditType::class, $user, ['method' => 'GET']);

        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $user = $editUserForm->getData();

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

            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/edit.html.twig', [
            'edit_user_form' => $editUserForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/add/user/password/{user}", name="add_user_password")
     */
    public function addPassword(Request $request, User $user, UserPasswordHasherInterface $encoder): Response
    {
        $addUserForm = $this->createForm(UserPasswordType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();
            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/add_password.html.twig', [
            'add_user_form' => $addUserForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/edit/password/{user}", name="edit_user_password")
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

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts', ['company' => $user->getCompanies()[0]->getId()]);
        }

        return $this->render('user/edit_password.html.twig', [
            'edit_user_form' => $editUserForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/delete/{user}", name="delete_user")
     */
    public function delete(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
