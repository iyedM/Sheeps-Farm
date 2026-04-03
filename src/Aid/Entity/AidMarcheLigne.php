<?php

namespace App\Aid\Entity;

use App\Aid\Repository\AidMarcheLigneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AidMarcheLigneRepository::class)]
#[ORM\Table(name: 'aid_marche_ligne')]
class AidMarcheLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AidMarche $marche = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AidLot $lot = null;

    #[ORM\Column]
    #[Assert\Positive]
    private int $quantiteAmenes = 1;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $quantiteVendus = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarche(): ?AidMarche
    {
        return $this->marche;
    }

    public function setMarche(?AidMarche $marche): static
    {
        $this->marche = $marche;

        return $this;
    }

    public function getLot(): ?AidLot
    {
        return $this->lot;
    }

    public function setLot(?AidLot $lot): static
    {
        $this->lot = $lot;

        return $this;
    }

    public function getQuantiteAmenes(): int
    {
        return $this->quantiteAmenes;
    }

    public function setQuantiteAmenes(int $quantiteAmenes): static
    {
        $this->quantiteAmenes = $quantiteAmenes;

        return $this;
    }

    public function getQuantiteVendus(): int
    {
        return $this->quantiteVendus;
    }

    public function setQuantiteVendus(int $quantiteVendus): static
    {
        $this->quantiteVendus = $quantiteVendus;

        return $this;
    }

    public function getMontantLigne(): float
    {
        if (!$this->lot) {
            return 0.0;
        }

        return $this->quantiteVendus * (float) $this->lot->getPrixUnitaire();
    }
}