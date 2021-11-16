<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Type;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Entity\Contract;
use App\Entity\GoogleAccount;
use App\Entity\FacebookAccount;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Company
        $companies = [];
        for ($i = 0; $i < 10; $i++) {
            $company = new Company();
            $company->setName($faker->company())
                ->setAddress($faker->address())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city());
            $companies[] = $company;
        }

        // Type
        $types = [];

        $googleAds = new Type();
        $googleAds->setName('GoogleAds')
            ->setLib('googleAds');
        $types[] = $googleAds;
        $manager->persist($googleAds);

        $youtube = new Type();
        $youtube->setName('Youtube')
            ->setLib('youtube');
        $types[] = $youtube;
        $manager->persist($youtube);

        $facebook = new Type();
        $facebook->setName('Facebook')
            ->setLib('facebook');
        $types[] = $facebook;
        $manager->persist($facebook);

        $contract = new Type();
        $contract->setName('Contrat')
            ->setLib('contract');
        $types[] = $contract;
        $manager->persist($contract);

        // User
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setRoles(['ROLE_USER'])
                ->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setContact($faker->phoneNumber())
                ->addCompany($companies[$i]);
            $companies[$i]->setUserId($user);
            $users[] = $user;
            $manager->persist($user);
            $manager->persist($companies[$i]);
        }

        // Contract
        $contracts = [];
        for ($i = 0; $i < 10; $i++) {
            $contract = new Contract();
            $contract->setName($faker->jobTitle())
                ->setPrice($faker->numberBetween(80, 210))
                ->setUserId($users[$i]);
            $nb = rand(0, 3);
            $contract->setType($types[$nb]);
            $types[$nb]->addContract($contract);
            $contracts[] = $contract;
            $manager->persist($contract);
        }

        // Invoice
        $invoices = [];
        for ($i = 0; $i < 10; $i++) {
            $invoice = new Invoice();
            $invoice->setNum($faker->lexify('id-?????'))
                ->setReleasedAt(new DateTimeImmutable())
                ->setContract($contracts[$i])
                ->setFile('/facture/test');
            $contracts[$i]->addInvoice($invoice);
            $invoices[] = $invoice;
            $manager->persist($invoice);
        }

        // FacebookAccount
        $fbAccounts = [];
        for ($i = 0; $i < 10; $i++) {
            $fbAccount = new FacebookAccount();
            $fbAccount->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setUserId($users[$i]);
            $users[$i]->setFacebookAccount($fbAccount);
            $fbAccounts[] = $fbAccount;
            $manager->persist($fbAccount);
        }

        // GoogleAccount
        $googleAccounts = [];
        for ($i = 0; $i < 10; $i++) {
            $googleAccount = new GoogleAccount();
            $googleAccount->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setUserId($users[$i]);
            $users[$i]->setGoogleAccount($googleAccount);
            $googleAccounts[] = $googleAccount;
            $manager->persist($googleAccount);
        }

        // Admin
        $admin = $this->createAdmin();
        $manager->persist($admin);

        // User
        $user = $this->createUser();
        $manager->persist($user);

        $manager->flush();
    }

    public function createAdmin(): User
    {
        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN'])
            ->setEmail('nicolas160796@gmail.com')
            ->setPassword('$2y$13$gOxWfP/wFyivsBfnKq1DGuRtWgGEYSFbBClRk3fcGxkXA54EYdQc6'); // test
        return $admin;
    }

    public function createUser(): User
    {
        $user = new User();
        $user->setRoles(['ROLE_USER'])
            ->setEmail('test@gmail.com')
            ->setPassword('$2y$13$gOxWfP/wFyivsBfnKq1DGuRtWgGEYSFbBClRk3fcGxkXA54EYdQc6'); // test
        return $user;
    }
}