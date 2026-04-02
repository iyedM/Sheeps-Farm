<?php

namespace App\Service;

use App\Entity\Grange;
use App\Repository\GrangeRepository;
use Doctrine\ORM\EntityManagerInterface;

class GrangeService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GrangeRepository $grangeRepository,
    ) {
    }

    public function save(Grange $grange): void
    {
        $this->em->persist($grange);
        $this->em->flush();
    }

    public function delete(Grange $grange): void
    {
        $this->em->remove($grange);
        $this->em->flush();
    }

    public function getStats(): array
    {
        return [
            'total' => $this->grangeRepository->countAll(),
            'actives' => $this->grangeRepository->countActives(),
        ];
    }
}
