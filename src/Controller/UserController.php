<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Company;
use App\Form\UserAddType;
use App\Form\UserEditType;
use App\Entity\GoogleAccount;
use App\Entity\FacebookAccount;
use App\Form\RegistrationFormType;
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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        return $this->render('admin/admin.html.twig', [
            'companies' => $companies
        ]);
    }

    // GENERAL //

    /**
     * @Route("/add/user", name="add_user")
     */
    public function add(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $addUserForm = $this->createForm(UserAddType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();
            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $company = $this->em->getRepository(Company::class)->findOneBy(['id' => $request->get('company')]);
            $company->addUser($user);
            $user->addCompany($company);

            $this->em->persist($user);
            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('admin');
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
    public function addForCompany(Request $request, Company $company, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $addUserForm = $this->createForm(RegistrationFormType::class, $user);
        $addUserForm->handleRequest($request);

        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $user = $addUserForm->getData();
            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded);

            $company->addUser($user);

            $this->em->persist($user);
            $this->em->persist($company);
            $this->em->flush();

            return $this->redirectToRoute('show_contracts', ['company' => $company->getId()]);
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
