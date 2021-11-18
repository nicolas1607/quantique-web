<?php

namespace App\Repository;

use App\Entity\TypeContract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeContract|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeContract|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeContract[]    findAll()
 * @method TypeContract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeContract::class);
    }

    // /**
    //  * @return TypeContract[] Returns an array of TypeContract objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeContract
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
