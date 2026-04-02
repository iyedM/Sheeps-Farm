<?php

namespace App\Service;

use App\Entity\Depense;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;

class DepenseService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DepenseRepository $depenseRepository,
    ) {
    }

    public function save(Depense $depense): void
    {
        $this->em->persist($depense);
        $this->em->flush();
    }

    public function delete(Depense $depense): void
    {
        $this->em->remove($depense);
        $this->em->flush();
    }

    public function getTotal(): float
    {
        return $this->depenseRepository->sumTotal();
    }
}
