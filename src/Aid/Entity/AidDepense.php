<?php

namespace App\Aid\Entity;

use App\Aid\Repository\AidDepenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AidDepenseRepository::class)]
#[ORM\Table(name: 'aid_depense')]
class AidDepense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private ?string $libelle = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero]
    private string $montant = '0.00';

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AidCampagne $campagne = null;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = trim($libelle);

        return $this;
    }

    public function getMontant(): float
    {
        return (float) $this->montant;
    }

    public function setMontant(string|float $montant): static
    {
        $this->montant = number_format((float) $montant, 2, '.', '');

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

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
}
