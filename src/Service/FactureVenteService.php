<?php

namespace App\Service;

use App\Entity\FactureVente;
use Doctrine\ORM\EntityManagerInterface;

class FactureVenteService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(FactureVente $facture): void
    {
        $total = $facture->getPrixAdditionnel();
        foreach ($facture->getMoutons() as $mouton) {
            $mouton->setEstVendu(true);
            $total += $mouton->getPrix();
            $this->em->persist($mouton);
        }

        $facture->setMontantTotal($total);

        $this->em->persist($facture);
        $this->em->flush();
    }

    public function delete(FactureVente $facture): void
    {
        $this->em->remove($facture);
        $this->em->flush();
    }
}
