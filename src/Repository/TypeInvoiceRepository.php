<?php

namespace App\Repository;

use App\Entity\TypeInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeInvoice[]    findAll()
 * @method TypeInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeInvoice::class);
    }

    // /**
    //  * @return TypeInvoice[] Returns an array of TypeInvoice objects
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
    public function findOneBySomeField($value): ?TypeInvoice
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
