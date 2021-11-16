<?php

namespace App\Controller;

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
     * @Route("/user/modify/{id}", name="modify_user")
     */
    public function modifyUser(Request $request, User $user, UserPasswordHasherInterface $encoder): Response
    {
        $modifyUserForm = $this->createForm(UserType::class, $user, ['method' => 'GET']);

        $modifyUserForm->handleRequest($request);

        if ($modifyUserForm->isSubmitted() && $modifyUserForm->isValid()) {
            $user = $modifyUserForm->getData();

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

            return $this->redirectToRoute('user');
        }

        return $this->render('user/modify.html.twig', [
            'modify_user_form' => $modifyUserForm->createView(),
            'user' => $user
        ]);
    }


    // ADMINISTRATEUR //

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
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
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/stats/{id}", name="show_stats_user")
     */
    public function showStats(User $user): Response
    {
        return $this->render('admin/show_stats.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/contracts/{id}", name="show_contracts_user")
     */
    public function showContracts(User $user): Response
    {
        return $this->render('admin/show_contracts.html.twig', [
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
        foreach ($user->getContracts() as $contract) {
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
        usort($res, function ($a, $b) {
            return $a < $b ? -1 : 1;
        });

        return $this->render('admin/show_invoices.html.twig', [
            'user' => $user,
            'invoices' => $res,
            'type' => $type
        ]);
    }
}
