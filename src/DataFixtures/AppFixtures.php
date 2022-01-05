<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\TypeInvoice;
use App\Entity\TypeContract;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // TypeContract
        $typesContract = [];

        $vitrine = new TypeContract();
        $vitrine->setName('Vitrine')
            ->setLib('vitrine');
        $manager->persist($vitrine);
        $typesContract[] = $vitrine;

        $ecommerce = new TypeContract();
        $ecommerce->setName('E-commerce')
            ->setLib('commerce');
        $manager->persist($ecommerce);
        $typesContract[] = $ecommerce;

        $google = new TypeContract();
        $google->setName('Google')
            ->setLib('google');
        $manager->persist($google);
        $typesContract[] = $google;

        $facebook = new TypeContract();
        $facebook->setName('Facebook')
            ->setLib('facebook');
        $manager->persist($facebook);
        $typesContract[] = $facebook;

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

        // Admin
        $admin = $this->createAdmin();
        $manager->persist($admin);
        $quantique = $this->createAdminQuantique();
        $manager->persist($quantique);

        $manager->flush();
    }

    public function createAdmin(): User
    {
        $admin = new User();
        $admin->setFirstname('Nicolas')
            ->setLastname('Mormiche')
            ->setPhone('0627712403')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('nicolas160796@gmail.com')
            ->setPassword('$2y$13$gOxWfP/wFyivsBfnKq1DGuRtWgGEYSFbBClRk3fcGxkXA54EYdQc6'); // test
        return $admin;
    }

    public function createAdminQuantique(): User
    {
        $admin = new User();
        $admin->setFirstname('Florent')
            ->setLastname('Pietrangeli')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('contact@quantique-web.fr')
            ->setPassword('$2y$13$2M/fJC1d0A9eQ3oiV9sB.OdVM2u0YntJVkvp9ukVKbDJTBCwX6Tbq'); // Quantique2021-
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
