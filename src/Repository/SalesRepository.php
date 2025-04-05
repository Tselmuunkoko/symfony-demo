<?php

namespace App\Repository;

use App\Entity\Sales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sales>
 */
class SalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sales::class);
    }

    public function findSalesByDateRange($prevStartDate, $startDate, $endDate)
    {
        return $this->getEntityManager()->createQuery(
            'SELECT 
                d.name AS department_name,
                COALESCE(SUM(CASE WHEN s.month BETWEEN :startDate AND :endDate THEN s.amount ELSE 0 END), 0) AS current,
                COALESCE(SUM(CASE WHEN s.month BETWEEN :prevStartDate AND :startDate THEN s.amount ELSE 0 END), 0) AS previous
            FROM App\Entity\Sales s
            JOIN s.department d
            GROUP BY d.name
            ORDER BY d.name DESC'
        )
        ->setParameter('startDate', $startDate)
        ->setParameter('prevStartDate', $prevStartDate)
        ->setParameter('endDate', $endDate)
        ->getResult(); 
    }
}
