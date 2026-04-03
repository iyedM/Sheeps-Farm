<?php

namespace App\Aid\Repository;

use App\Aid\Entity\AidMarcheLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AidMarcheLigne>
 */
class AidMarcheLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AidMarcheLigne::class);
    }
}