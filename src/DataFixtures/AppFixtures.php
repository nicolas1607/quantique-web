<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Contract;
use App\Entity\TypeInvoice;
use App\Entity\TypeContract;
use App\Entity\GoogleAccount;
use App\Entity\FacebookAccount;
use App\Entity\Website;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // // User
        // $users = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $user = new User();
        //     $user->setFirstname($faker->firstName())
        //         ->setLastname($faker->lastName())
        //         ->setRoles(['ROLE_USER'])
        //         ->setEmail($faker->email())
        //         ->setPassword($faker->password())
        //         ->setPhone($faker->phoneNumber());

        //     $manager->persist($user);
        //     $users[] = $user;
        // }

        // // Company
        // $companies = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $company = new Company();
        //     $company->setName($faker->company())
        //         ->setEmail($faker->email())
        //         ->setPhone($faker->phoneNumber())
        //         ->setAddress($faker->address())
        //         ->setPostalCode($faker->postcode())
        //         ->setCity($faker->city())
        //         ->setNumTVA($faker->regexify('[A-Z]{5}[0-4]{3}'))
        //         ->setSiret($faker->regexify('[A-Z]{5}[0-4]{3}'))
        //         ->addUser($users[$i]);
        //     $users[$i]->addCompany($company);

        //     $manager->persist($company);
        //     $manager->persist($users[$i]);
        //     $companies[] = $company;
        // }

        // // Website
        // $websites = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $website = new Website();
        //     $website->setName($faker->company())
        //         ->setUrl($faker->url())
        //         ->setCompany($companies[$i]);
        //     $companies[$i]->addWebsite($website);

        //     $manager->persist($website);
        //     $manager->persist($companies[$i]);
        //     $websites[] = $website;
        // }

        // TypeContract
        $typesContract = [];

        $vitrine = new TypeContract();
        $vitrine->setName('Site vitrine')
            ->setLib('vitrine');
        $manager->persist($vitrine);
        $typesContract[] = $vitrine;

        $ecommerce = new TypeContract();
        $ecommerce->setName('Site e-commerce')
            ->setLib('commerce');
        $manager->persist($ecommerce);
        $typesContract[] = $ecommerce;

        $google = new TypeContract();
        $google->setName('Publicité Google & Youtube')
            ->setLib('google');
        $manager->persist($google);
        $typesContract[] = $google;

        $facebook = new TypeContract();
        $facebook->setName('Publicité Facebook & Instagram')
            ->setLib('facebook');
        $manager->persist($facebook);
        $typesContract[] = $facebook;

        // // Contract
        // $contracts = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $type = $typesContract[rand(0, 3)];
        //     $contract = new Contract();
        //     $contract->setPrice($faker->randomNumber(3, true))
        //         ->setPromotion($faker->randomFloat(1, 20, 30))
        //         ->setType($type)
        //         ->setWebsite($websites[$i]);

        //     $type->addContract($contract);
        //     $websites[$i]->addContract($contract);

        //     $manager->persist($contract);
        //     $manager->persist($type);
        //     $manager->persist($websites[$i]);
        //     $contracts[] = $contract;
        // }
        // for ($i = 0; $i < 10; $i++) {
        //     $type = $typesContract[rand(0, 3)];
        //     $contract = new Contract();
        //     $contract->setPrice($faker->randomNumber(3, true))
        //         ->setPromotion($faker->randomFloat(1, 20, 30))
        //         ->setType($type)
        //         ->setWebsite($websites[$i]);

        //     $type->addContract($contract);
        //     $websites[$i]->addContract($contract);

        //     $manager->persist($type);
        //     $manager->persist($websites[$i]);
        //     $manager->persist($contract);
        //     $contracts[] = $contract;
        // }

        // TypeInvoice
        $typesInvoice = [];

        $abonnement = new TypeInvoice();
        $abonnement->setName('Abonnement');
        $manager->persist($abonnement);
        $typesInvoice[] = $abonnement;

        $perso = new TypeInvoice();
        $perso->setName('Personnalisée');
        $manager->persist($perso);
        $typesInvoice[] = $perso;

        // // Invoice
        // $invoices = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $type = $typesInvoice[rand(0, 1)];
        //     $invoice = new Invoice();
        //     $invoice->setReleasedAt(new DateTime())
        //         ->setFile('/facture/test')
        //         ->setType($type)
        //         ->addWebsite($websites[$i]);

        //     $type->addInvoice($invoice);
        //     $websites[$i]->addInvoice($invoice);

        //     $manager->persist($invoice);
        //     $manager->persist($type);
        //     $manager->persist($websites[$i]);
        //     $invoices[] = $invoice;
        // }

        // // FacebookAccount
        // $fbAccounts = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $fbAccount = new FacebookAccount();
        //     $fbAccount->setEmail($faker->email())
        //         ->setPassword($faker->password())
        //         ->setCompany($companies[$i]);
        //     $companies[$i]->addFacebookAccount($fbAccount);
        //     $manager->persist($fbAccount);
        //     $manager->persist($companies[$i]);
        //     $fbAccounts[] = $fbAccount;
        // }

        // // GoogleAccount
        // $googleAccounts = [];
        // for ($i = 0; $i < 10; $i++) {
        //     $googleAccount = new GoogleAccount();
        //     $googleAccount->setEmail($faker->email())
        //         ->setPassword($faker->password())
        //         ->setCompany($companies[$i]);
        //     $manager->persist($googleAccount);
        //     $manager->persist($companies[$i]);
        //     $googleAccounts[] = $googleAccount;
        // }

        // Admin
        $admin = $this->createAdmin();
        $manager->persist($admin);

        // User
        // $user = $this->createUser();
        // $manager->persist($user);

        $manager->flush();
    }

    public function createAdmin(): User
    {
        $admin = new User();
        $admin->setFirstname('Nicolas')
            ->setLastname('Mormiche')
            ->setPhone('06 27 71 24 03')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('nicolas160796@gmail.com')
            ->setPassword('$2y$13$gOxWfP/wFyivsBfnKq1DGuRtWgGEYSFbBClRk3fcGxkXA54EYdQc6'); // test
        return $admin;
    }

    public function createUser(): User
    {
        $user = new User();
        $user->setFirstname('Florent')
            ->setLastname('Pietrangeli')
            ->setPhone('06 19 72 24 10')
            ->setRoles(['ROLE_USER'])
            ->setEmail('test@gmail.com')
            ->setPassword('$2y$13$gOxWfP/wFyivsBfnKq1DGuRtWgGEYSFbBClRk3fcGxkXA54EYdQc6'); // test
        return $user;
    }
}
