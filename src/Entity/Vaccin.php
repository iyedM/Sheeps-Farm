<?php

namespace App\Entity;

use App\Repository\VaccinRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VaccinRepository::class)]
class Vaccin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateVaccination = null;

    #[ORM\ManyToOne(inversedBy: 'vaccins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mouton $mouton = null;

    public function __construct()
    {
        $this->dateVaccination = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDateVaccination(): ?\DateTimeImmutable { return $this->dateVaccination; }
    public function setDateVaccination(\DateTimeImmutable $dateVaccination): static { $this->dateVaccination = $dateVaccination; return $this; }
    public function getMouton(): ?Mouton { return $this->mouton; }
    public function setMouton(?Mouton $mouton): static { $this->mouton = $mouton; return $this; }
}
