<?php

namespace App\Entity;

use App\Repository\FactureAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureAchatRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FactureAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $fournisseur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAchat = null;

    #[ORM\Column]
    private float $totalGlobal = 0.0;

    /** @var Collection<int, SousLotAchat> */
    #[ORM\OneToMany(mappedBy: 'factureAchat', targetEntity: SousLotAchat::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $sousLots;

    public function __construct()
    {
        $this->sousLots = new ArrayCollection();
        $this->dateAchat = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeTotalGlobal(): void
    {
        $this->totalGlobal = 0.0;
        foreach ($this->sousLots as $sousLot) {
            $this->totalGlobal += $sousLot->getSousTotal();
        }
    }

    public function getId(): ?int { return $this->id; }
    public function getFournisseur(): ?string { return $this->fournisseur; }
    public function setFournisseur(string $fournisseur): static { $this->fournisseur = $fournisseur; return $this; }
    public function getDateAchat(): ?\DateTimeImmutable { return $this->dateAchat; }
    public function setDateAchat(\DateTimeImmutable $dateAchat): static { $this->dateAchat = $dateAchat; return $this; }
    public function getTotalGlobal(): float { return $this->totalGlobal; }
    public function setTotalGlobal(float $totalGlobal): static { $this->totalGlobal = $totalGlobal; return $this; }

    /** @return Collection<int, SousLotAchat> */
    public function getSousLots(): Collection { return $this->sousLots; }
    public function addSousLot(SousLotAchat $sousLot): static
    {
        if (!$this->sousLots->contains($sousLot)) { $this->sousLots->add($sousLot); $sousLot->setFactureAchat($this); }
        return $this;
    }
    public function removeSousLot(SousLotAchat $sousLot): static
    {
        if ($this->sousLots->removeElement($sousLot) && $sousLot->getFactureAchat() === $this) { $sousLot->setFactureAchat(null); }
        return $this;
    }
}
