<?php

namespace App\Controller;

use DateTime;
use App\Entity\Note;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Website;
use App\Entity\Contract;
use App\Form\CompanyType;
use App\Form\InvoiceType;
use App\Entity\TypeInvoice;
use App\Entity\TypeContract;
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
    private UserRepository $userRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/admin/companies", name="admin_companies")
     */
    public function companies(Request $request, MailerInterface $mailer, SluggerInterface $slugger): Response
    {
        $companies = $this->em->getRepository(Company::class)->findAll();

        $users = $this->userRepo->findUsers();
        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();
        $typesInvoice = $this->em->getRepository(TypeInvoice::class)->findAll();
        $currentDate = new DateTime();

        // Formulaire d'ajout d'entreprise
        $company = new Company();
        if ($request->get('name') && $request->get('email')) {
            $company->setName($request->get('name'))
                ->setEmail($request->get('email'));
            if ($request->get('phone')) $company->setPhone($request->get('phone'));
            if ($request->get('address')) $company->setAddress($request->get('address'));
            if ($request->get('postalCode')) $company->setPostalCode($request->get('postalCode'));
            if ($request->get('city')) $company->setCity($request->get('city'));
            if ($request->get('numTVA')) $company->setNumTVA($request->get('numTVA'));
            if ($request->get('siret')) $company->setSiret($request->get('siret'));

            // Abonnement
            $website = null;
            if ($request->get('website') != '') {
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
            $this->addFlash('success', $company->getName() . ' ajoutée avec succès !');
            return $this->redirectToRoute('admin_companies');
        }

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
                        'company' => $company,
                        'invoices' => $invoices
                    ]);
                foreach ($invoices as $invoice) {
                    $email->attachFromPath($this->getParameter('invoices') . '/' . $invoice->getFile());
                }
                $mailer->send($email);
            }
            $this->addFlash('success', 'Facture(s) ajoutée(s) à ' . $company->getName() . ' !');
        }

        $typesContract = $this->em->getRepository(TypeContract::class)->findAll();

        return $this->render('admin/companies.html.twig', [
            'users' => $users,
            'companies' => $companies,
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
            $user->setPassword($passwordEncoded)
                ->setToken(md5(uniqid()));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès !');
        } else if ($editPasswordForm->isSubmitted() && !$editPasswordForm->isValid()) {
            $this->addFlash('alert', 'Les mots de passe doivent correspondre !');
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
        $notes = $this->em->getRepository(Note::class)->findAll();
        return $this->render('admin/notes.html.twig', [
            'notes' => $notes,
        ]);
    }

    /**
     * @Route("/admin/user/add/modal", name="add_user_modal")
     */
    public function addFromModal(Request $request, UserPasswordHasherInterface $encoder, MailerInterface $mailer): Response
    {
        $email = $request->get('email');
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        // verif si email valide
        if ($email && !preg_match("^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^", $email)) {
            $this->addFlash('alert', 'Veuillez saisir une adresse email valide !');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        // verif si email inexistant
        else if ($user) {
            $this->addFlash('alert', 'L\'adresse email suivante existe déjà : ' . $email);
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        // verif si newUser ou existUser à ajouter
        else {
            $user = new User;
            $userexist = new User;
            if ($request->get('user')) {
                $userexist = $this->em->getRepository(User::class)->findOneBy([
                    'id' => $request->get('user')
                ]);
            }
            if ($request->get('firstname') && $request->get('lastname') && $request->get('email')) {
                $user->setFirstname($request->get('firstname'))
                    ->setLastname($request->get('lastname'))
                    ->setEmail($request->get('email'))
                    ->setPhone($request->get('phone'))
                    ->setToken(md5(uniqid()));
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

            if ($user->getId() && $userexist->getId()) {
                $this->addFlash('success', 'Clients ajoutés à ' . $company->getName() . ' !');
            } else {
                $this->addFlash('success', 'Client ajouté à ' . $company->getName() . ' !');
            }

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * @Route("/profile/user/edit/modal/{user}", name="edit_user_modal")
     */
    public function edit(Request $request, User $user): Response
    {
        $email = $request->get('email');
        if (!preg_match("^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^", $email)) {
            $this->addFlash('alert', 'Veuillez saisir une adresse email valide !');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $user->setFirstname($request->get('firstname'))
                ->setLastname($request->get('lastname'))
                ->setEmail($request->get('email'))
                ->setPhone($request->get('phone'));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Modification prise en compte !');
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/reset/password", name="reset_user_password")
     */
    public function resetPassword(): Response
    {
        return $this->render('security/reset_password.html.twig');
    }

    /**
     * @Route("/reset/password/mail", name="send_reset_mail")
     */
    public function sendResetMail(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy([
            'email' => $request->get('email')
        ]);

        if ($user) {
            $email = (new TemplatedEmail())
                ->from('noreply@quantique-web.fr')
                ->to($user->getEmail())
                ->subject('Réinitialiser mon mot de passe !')
                ->htmlTemplate('emails/reset_password.html.twig')
                ->context([
                    'user' => $user
                ]);
            $mailer->send($email);
            $this->addFlash('success', 'Vérifier votre adresse email pour réinitialiser votre mot de passe !');
            return $this->redirectToRoute('app_login');
        } else {
            $this->addFlash('alert', 'Aucun compte ne correspnd à cette adresse email');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * @Route("/reset/password/{token}", name="reset_user_password_with_token")
     */
    public function resetPasswordWithToken(Request $request, String $token, UserPasswordHasherInterface $encoder): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $token]);
        $editUserForm = $this->createForm(UserPasswordType::class, $user);
        $editUserForm->handleRequest($request);

        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $user = $editUserForm->getData();

            $password = $user->getPassword();
            $passwordEncoded = $encoder->hashPassword($user, $password);
            $user->setPassword($passwordEncoded)
                ->setNbConnection($user->getNbConnection() + 1)
                ->setToken(md5(uniqid()));

            $this->em->persist($user);
            $this->em->flush();

            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login');
            } else if ($this->getUser()->getCompanies()[0]) {
                return $this->redirectToRoute('show_contracts', [
                    'company' => $this->getUser()->getCompanies()[0]->getId()
                ]);
            } else {
                return $this->redirectToRoute('nothing', [
                    'user' => $user->getId()
                ]);
            }
        } else if ($editUserForm->isSubmitted() && !$editUserForm->isValid()) {
            $this->addFlash('alert', 'Les mots de passe doivent correspondre !');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }


        return $this->render('user/edit_password.html.twig', [
            'user' => $user,
            'edit_user_form' => $editUserForm->createView()
        ]);
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
            $user->setPassword($passwordEncoded)
                ->setNbConnection($user->getNbConnection() + 1)
                ->setToken(md5(uniqid()));

            $this->em->persist($user);
            $this->em->flush();

            if ($this->getUser()->getCompanies()[0]) {
                return $this->redirectToRoute('show_contracts', [
                    'company' => $this->getUser()->getCompanies()[0]->getId()
                ]);
            } else {
                return $this->redirectToRoute('nothing', [
                    'user' => $user->getId()
                ]);
            }

            $this->addFlash('success', 'Mot de passe modifié avec succès !');
        } else if ($editUserForm->isSubmitted() && !$editUserForm->isValid()) {
            $this->addFlash('alert', 'Les mots de passe doivent correspondre !');
            return $this->redirect($_SERVER['HTTP_REFERER']);
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

        $this->addFlash(
            'success',
            $user->getFirstname() . ' ' . $user->getLastname() . ' supprimé(e) avec succès !'
        );

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/profile", name="nothing")
     */
    public function nothing(): Response
    {
        return $this->render('user/nothing.html.twig');
    }
}
