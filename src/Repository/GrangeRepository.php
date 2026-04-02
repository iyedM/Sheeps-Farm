<?php

namespace App\Repository;

use App\Entity\Grange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grange>
 */
class GrangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grange::class);
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('g')->select('COUNT(g.id)')->getQuery()->getSingleScalarResult();
    }

    public function countActives(): int
    {
        return (int) $this->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id)')
            ->leftJoin('g.moutons', 'm')
            ->andWhere('m.estVendu = false')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
