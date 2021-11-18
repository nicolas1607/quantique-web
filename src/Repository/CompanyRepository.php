<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    // /**
    //  * @return Company[] Returns an array of Company objects
    //  */
    public function findNbContract(Company $company)
    {
        return $this->getEntityManager()->createQuery(
            "SELECT count(c) FROM App:company cmp
            INNER JOIN App:website w
            WITH w.company = cmp.id
            INNER JOIN App:contract c
            WITH c.website = w.id
            WHERE cmp.id = " . $company->getId()
        )->getResult()[0][1];
    }
}
