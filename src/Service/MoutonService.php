<?php

namespace App\Service;

use App\Entity\Mouton;
use App\Repository\MoutonRepository;
use Doctrine\ORM\EntityManagerInterface;

class MoutonService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MoutonRepository $moutonRepository,
    ) {
    }

    public function save(Mouton $mouton): void
    {
        $this->em->persist($mouton);
        $this->em->flush();
    }

    public function delete(Mouton $mouton): void
    {
        $this->em->remove($mouton);
        $this->em->flush();
    }

    public function buildFilterQuery(array $filters)
    {
        return $this->moutonRepository->getFilterQueryBuilder($filters);
    }
}
