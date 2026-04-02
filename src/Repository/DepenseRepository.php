<?php

namespace App\Repository;

use App\Entity\Depense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    public function sumTotal(): float
    {
        return (float) ($this->createQueryBuilder('d')->select('COALESCE(SUM(d.montant), 0)')->getQuery()->getSingleScalarResult() ?? 0);
    }
}
