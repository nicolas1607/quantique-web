<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
                return $this->redirectToRoute('admin_companies');
            } else {
                if ($this->getUser()->getNbConnection() == null) {
                    return $this->redirectToRoute('edit_user_password', ['user' => $this->getUser()->getId()]);
                } else if ($this->getUser()->getCompanies()[0]) {
                    return $this->redirectToRoute('show_contracts', [
                        'company' => $this->getUser()->getCompanies()[0]->getId()
                    ]);
                } else {
                    return $this->redirectToRoute('nothing');
                }
            }
        }
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
