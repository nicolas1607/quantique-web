<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Invoice;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    // /**
    //  * @return Invoice[] Retournes les factures d'une compagnie ordonnées par date
    //  */
    public function findOrderByDate(Company $company)
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT i FROM App:invoice i
                WHERE i.company = " . $company->getId() . "
                ORDER BY i.releasedAt DESC"
            )
            ->getResult();
    }
}
