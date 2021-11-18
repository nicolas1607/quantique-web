<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\GoogleAccount;
use App\Entity\FacebookAccount;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
    }


    // UTILISATEUR //

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig', []);
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user")
     */
    public function editUser(Request $request, User $user, UserPasswordHasherInterface $encoder): Response
    {
        $editUserForm = $this->createForm(UserType::class, $user, ['method' => 'GET']);

        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $user = $editUserForm->getData();

            // On créer les comptes Google & Facebook
            $emailGoogle = $request->query->get('emailGoogle');
            $mdpGoogle = $request->query->get('mdpGoogle');
            if ($emailGoogle && $mdpGoogle) {
                $accountGoogle = new GoogleAccount();
                $accountGoogle->setEmail($emailGoogle)
                    ->setPassword($encoder->hashPassword($user, $mdpGoogle));
                $user->setGoogleAccount($accountGoogle);
                $this->em->persist($accountGoogle);
            }
            $emailFb = $request->query->get('emailFb');
            $mdpFb = $request->query->get('mdpFb');
            if ($emailFb && $mdpFb) {
                $accountFb = new FacebookAccount();
                $accountFb->setEmail($emailFb)
                    ->setPassword($encoder->hashPassword($user, $mdpFb));
                $user->setGoogleFacebook($accountFb);
                $this->em->persist($accountFb);
            }

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


    // ADMINISTRATEUR USER //

    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function adminUsers(): Response
    {
        $users = [];
        foreach ($this->userRepo->findAll() as $user) {
            $flag = true;
            foreach ($user->getRoles() as $role) {
                if ($role == 'ROLE_ADMIN') {
                    $flag = false;
                }
            }
            if ($flag) {
                $users[] = $user;
            }
        }
        return $this->render('admin/users/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/companies/{id}", name="show_companies_user")
     */
    public function showCompanies(User $user): Response
    {
        return $this->render('admin/users/show_companies.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/websites/{id}", name="show_websites_user")
     */
    public function showWebsite(User $user): Response
    {
        return $this->render('admin/users/show_websites.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/contracts/{id}", name="show_contracts_user")
     */
    public function showContracts(User $user): Response
    {
        return $this->render('admin/users/show_contracts.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/invoices/{id}", name="show_invoices_user")
     */
    public function showInvoices(Request $request, User $user): Response
    {
        $type = $request->query->get('typeContract');
        if ($type == null) $type = 'all';

        $res = [];
        foreach ($user->getCompanies() as $company) {
            foreach ($company->getContracts() as $contract) {
                foreach ($contract->getInvoices() as $invoice) {
                    if ($type != 'all') {
                        if ($invoice->getContract()->getType()->getLib() == $type) {
                            $res[] = $invoice;
                        }
                    } else {
                        $res[] = $invoice;
                    }
                }
            }
        }
        usort($res, function ($a, $b) {
            return $a < $b ? -1 : 1;
        });

        return $this->render('admin/users/show_invoices.html.twig', [
            'user' => $user,
            'invoices' => $res,
            'type' => $type
        ]);
    }

    // ADMINISTRATION COMPANY //

    /**
     * @Route("/admin/companies", name="admin_companies")
     */
    public function adminCompanies(): Response
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        return $this->render('admin/companies/companies.html.twig', [
            'companies' => $companies
        ]);
    }
}
