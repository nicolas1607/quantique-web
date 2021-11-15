<?php

namespace App\Repository;

use App\Entity\FacebookAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FacebookAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacebookAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacebookAccount[]    findAll()
 * @method FacebookAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacebookAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacebookAccount::class);
    }

    // /**
    //  * @return FacebookAccount[] Returns an array of FacebookAccount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FacebookAccount
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
