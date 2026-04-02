<?php

namespace App\Entity;

use App\Repository\CommerceVenteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommerceVenteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CommerceVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $client = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $numeroClient = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $race = null;

    #[ORM\Column]
    #[Assert\Positive]
    private int $quantite = 1;

    #[ORM\Column]
    private float $prixUnitaire = 0.0;

    #[ORM\Column]
    private float $prixAdditionnel = 0.0;

    #[ORM\Column]
    private float $prixTotal = 0.0;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateVente = null;

    public function __construct()
    {
        $this->dateVente = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computePrixTotal(): void
    {
        $this->prixTotal = ($this->prixUnitaire * $this->quantite) + $this->prixAdditionnel;
    }

    public function getId(): ?int { return $this->id; }
    public function getClient(): ?string { return $this->client; }
    public function setClient(string $client): static { $this->client = $client; return $this; }
    public function getNumeroClient(): ?string { return $this->numeroClient; }
    public function setNumeroClient(string $numeroClient): static { $this->numeroClient = $numeroClient; return $this; }
    public function getRace(): ?string { return $this->race; }
    public function setRace(string $race): static { $this->race = $race; return $this; }
    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }
    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): static { $this->prixUnitaire = $prixUnitaire; return $this; }
    public function getPrixAdditionnel(): float { return $this->prixAdditionnel; }
    public function setPrixAdditionnel(float $prixAdditionnel): static { $this->prixAdditionnel = $prixAdditionnel; return $this; }
    public function getPrixTotal(): float { return $this->prixTotal; }
    public function setPrixTotal(float $prixTotal): static { $this->prixTotal = $prixTotal; return $this; }
    public function getDateVente(): ?\DateTimeImmutable { return $this->dateVente; }
    public function setDateVente(\DateTimeImmutable $dateVente): static { $this->dateVente = $dateVente; return $this; }
}
