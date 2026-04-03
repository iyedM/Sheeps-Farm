<?php

namespace App\Aid\Repository;

use App\Aid\Entity\AidCampagne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AidCampagne>
 */
class AidCampagneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AidCampagne::class);
    }

    /**
     * @return AidCampagne[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.lots', 'l')->addSelect('l')
            ->leftJoin('c.depenses', 'd')->addSelect('d')
            ->leftJoin('c.marches', 'm')->addSelect('m')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
