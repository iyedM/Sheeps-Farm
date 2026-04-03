<?php

namespace App\Aid\Entity;

use App\Aid\Repository\AidLotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AidLotRepository::class)]
#[ORM\Table(name: 'aid_lot')]
class AidLot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive]
    private int $quantite = 1;

    #[ORM\Column]
    #[Assert\Positive]
    private int $ageMois = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive]
    private string $prixUnitaire = '0.00';

    #[ORM\ManyToOne(inversedBy: 'lots')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AidCampagne $campagne = null;

    /** @var Collection<int, AidMarcheLigne> */
    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: AidMarcheLigne::class)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getAgeMois(): int
    {
        return $this->ageMois;
    }

    public function setAgeMois(int $ageMois): static
    {
        $this->ageMois = $ageMois;

        return $this;
    }

    public function getPrixUnitaire(): string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = number_format((float) $prixUnitaire, 2, '.', '');

        return $this;
    }

    public function getCampagne(): ?AidCampagne
    {
        return $this->campagne;
    }

    public function setCampagne(?AidCampagne $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getTotal(): float
    {
        return (float) $this->prixUnitaire * $this->quantite;
    }

    /** @return Collection<int, AidMarcheLigne> */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function getQuantiteAffectee(): int
    {
        $total = 0;

        foreach ($this->lignes as $ligne) {
            $total += $ligne->getQuantiteAmenes();
        }

        return $total;
    }

    public function getQuantiteRestante(): int
    {
        return max(0, $this->quantite - $this->getQuantiteAffectee());
    }
}
