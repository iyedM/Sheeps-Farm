<?php

namespace App\Entity;

use App\Repository\SousLotAchatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SousLotAchatRepository::class)]
class SousLotAchat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $race = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 120)]
    private ?int $age = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?float $prix = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $quantite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Grange $grange = null;

    #[ORM\ManyToOne(inversedBy: 'sousLots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FactureAchat $factureAchat = null;

    public function getId(): ?int { return $this->id; }
    public function getRace(): ?string { return $this->race; }
    public function setRace(string $race): static { $this->race = $race; return $this; }
    public function getAge(): ?int { return $this->age; }
    public function setAge(?int $age): static { $this->age = $age; return $this; }
    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(?string $genre): static { $this->genre = $genre; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(?float $prix): static { $this->prix = $prix; return $this; }
    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(?int $quantite): static { $this->quantite = $quantite; return $this; }
    public function getGrange(): ?Grange { return $this->grange; }
    public function setGrange(?Grange $grange): static { $this->grange = $grange; return $this; }
    public function getFactureAchat(): ?FactureAchat { return $this->factureAchat; }
    public function setFactureAchat(?FactureAchat $factureAchat): static { $this->factureAchat = $factureAchat; return $this; }

    public function getSousTotal(): float
    {
        return $this->prix * $this->quantite;
    }
}
