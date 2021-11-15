<?php

namespace App\Repository;

use App\Entity\GoogleAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoogleAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleAccount[]    findAll()
 * @method GoogleAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleAccount::class);
    }

    // /**
    //  * @return GoogleAccount[] Returns an array of GoogleAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GoogleAccount
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
