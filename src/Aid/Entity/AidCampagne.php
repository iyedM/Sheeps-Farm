<?php

namespace App\Aid\Entity;

use App\Aid\Repository\AidCampagneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AidCampagneRepository::class)]
#[ORM\Table(name: 'aid_campagne')]
class AidCampagne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, AidLot> */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: AidLot::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lots;

    /** @var Collection<int, AidDepense> */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: AidDepense::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $depenses;

    /** @var Collection<int, AidMarche> */
    #[ORM\OneToMany(mappedBy: 'campagne', targetEntity: AidMarche::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $marches;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->lots = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->marches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = trim($nom);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** @return Collection<int, AidLot> */
    public function getLots(): Collection
    {
        return $this->lots;
    }

    public function addLot(AidLot $lot): static
    {
        if (!$this->lots->contains($lot)) {
            $this->lots->add($lot);
            $lot->setCampagne($this);
        }

        return $this;
    }

    public function removeLot(AidLot $lot): static
    {
        if ($this->lots->removeElement($lot) && $lot->getCampagne() === $this) {
            $lot->setCampagne(null);
        }

        return $this;
    }

    /** @return Collection<int, AidDepense> */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(AidDepense $depense): static
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses->add($depense);
            $depense->setCampagne($this);
        }

        return $this;
    }

    public function removeDepense(AidDepense $depense): static
    {
        if ($this->depenses->removeElement($depense) && $depense->getCampagne() === $this) {
            $depense->setCampagne(null);
        }

        return $this;
    }

    /** @return Collection<int, AidMarche> */
    public function getMarches(): Collection
    {
        return $this->marches;
    }

    public function addMarche(AidMarche $marche): static
    {
        if (!$this->marches->contains($marche)) {
            $this->marches->add($marche);
            $marche->setCampagne($this);
        }

        return $this;
    }

    public function removeMarche(AidMarche $marche): static
    {
        if ($this->marches->removeElement($marche) && $marche->getCampagne() === $this) {
            $marche->setCampagne(null);
        }

        return $this;
    }

    public function getStockInitial(): int
    {
        $total = 0;

        foreach ($this->lots as $lot) {
            $total += $lot->getQuantite();
        }

        return $total;
    }

    public function getMoutonsVendus(): int
    {
        $total = 0;

        foreach ($this->marches as $marche) {
            $total += $marche->getMoutonsVendus();
        }

        return $total;
    }

    public function getMoutonsRestants(): int
    {
        return max(0, $this->getStockInitial() - $this->getMoutonsVendus());
    }

    public function getWeightedAveragePrice(): float
    {
        $stock = 0;
        $sum = 0.0;

        foreach ($this->lots as $lot) {
            $stock += $lot->getQuantite();
            $sum += $lot->getQuantite() * $lot->getPrixUnitaire();
        }

        return $stock > 0 ? $sum / $stock : 0.0;
    }

    public function getTotalRecettes(): float
    {
        $total = 0.0;

        foreach ($this->marches as $marche) {
            $total += $marche->getTotalNet();
        }

        return $total;
    }

    public function getTotalDepenses(): float
    {
        $total = 0.0;

        foreach ($this->depenses as $depense) {
            $total += $depense->getMontant();
        }

        return $total;
    }

    public function getCapitalNet(): float
    {
        return $this->getTotalRecettes() - $this->getTotalDepenses();
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
}
