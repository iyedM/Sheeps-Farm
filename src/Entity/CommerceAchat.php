<?php

namespace App\Entity;

use App\Repository\CommerceAchatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommerceAchatRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CommerceAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $fournisseur = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $numeroFournisseur = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $race = null;

    #[ORM\Column]
    #[Assert\Positive]
    private int $quantite = 1;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private float $prixUnitaire = 0.0;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank]
    private ?string $genre = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 120)]
    private ?int $age = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateAchat = null;

    #[ORM\Column]
    private float $prixTotal = 0.0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Grange $grange = null;



    public function __construct()
    {
        $this->dateAchat = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computePrixTotal(): void
    {
        $this->prixTotal = $this->prixUnitaire * $this->quantite;
    }

    public function getId(): ?int { return $this->id; }
    public function getFournisseur(): ?string { return $this->fournisseur; }
    public function setFournisseur(string $fournisseur): static { $this->fournisseur = $fournisseur; return $this; }
    public function getNumeroFournisseur(): ?string { return $this->numeroFournisseur; }
    public function setNumeroFournisseur(string $numeroFournisseur): static { $this->numeroFournisseur = $numeroFournisseur; return $this; }
    public function getRace(): ?string { return $this->race; }
    public function setRace(string $race): static { $this->race = $race; return $this; }
    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }
    public function getPrixUnitaire(): float { return $this->prixUnitaire; }
    public function setPrixUnitaire(float $prixUnitaire): static { $this->prixUnitaire = $prixUnitaire; return $this; }
    public function getDateAchat(): ?\DateTimeImmutable { return $this->dateAchat; }
    public function setDateAchat(\DateTimeImmutable $dateAchat): static { $this->dateAchat = $dateAchat; return $this; }
    public function getPrixTotal(): float { return $this->prixTotal; }
    public function setPrixTotal(float $prixTotal): static { $this->prixTotal = $prixTotal; return $this; }

    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(?string $genre): static { $this->genre = $genre; return $this; }
    public function getAge(): ?int { return $this->age; }
    public function setAge(?int $age): static { $this->age = $age; return $this; }
    public function getGrange(): ?Grange { return $this->grange; }
    public function setGrange(?Grange $grange): static { $this->grange = $grange; return $this; }
}
