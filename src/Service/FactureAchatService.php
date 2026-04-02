<?php

namespace App\Service;

use App\Entity\FactureAchat;
use Doctrine\ORM\EntityManagerInterface;

class FactureAchatService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(FactureAchat $facture): void
    {
        $facture->computeTotalGlobal();
        $this->em->persist($facture);
        $this->em->flush();
    }

    public function delete(FactureAchat $facture): void
    {
        $this->em->remove($facture);
        $this->em->flush();
    }
}
