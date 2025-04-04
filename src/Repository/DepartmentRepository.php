<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Department>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function findSalesByDepartment($departmentId)
    {
        return $this->createQueryBuilder('d')
        ->select('s.id AS saleId', 's.month AS saleMonth', 's.amount AS saleAmount')
        ->innerJoin('d.sales', 's')
        ->where('d.id = :departmentId')
        ->setParameter('departmentId', $departmentId)
        ->getQuery()
        ->getResult();
    }

    public function findSalesByDepartmentIdAndMonth($departmentId, $startDate, $endDate)
    {
        return $this->createQueryBuilder('d')
        ->select('s.id AS saleId', 's.month AS saleMonth', 's.amount AS saleAmount')
        ->innerJoin('d.sales', 's')
        ->where('d.id = :departmentId')
        ->andWhere('s.month BETWEEN :startDate AND :endDate')
        ->setParameter('departmentId', $departmentId)
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate)
        ->getQuery()
        ->getResult();
    }
}
