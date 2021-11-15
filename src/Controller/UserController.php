<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

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
        return $this->render('admin/admin.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig', []);
    }
}
