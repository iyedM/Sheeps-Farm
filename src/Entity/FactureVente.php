<?php

namespace App\Entity;

use App\Repository\FactureVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureVenteRepository::class)]
class FactureVente
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

    #[ORM\Column]
    private ?\DateTimeImmutable $dateVente = null;

    #[ORM\Column]
    private float $montantTotal = 0.0;

    #[ORM\Column]
    private float $prixAdditionnel = 0.0;

    /** @var Collection<int, Mouton> */
    #[ORM\ManyToMany(targetEntity: Mouton::class, inversedBy: 'factureVentes')]
    #[ORM\JoinTable(name: 'facture_vente_mouton')]
    private Collection $moutons;

    public function __construct()
    {
        $this->moutons = new ArrayCollection();
        $this->dateVente = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getClient(): ?string { return $this->client; }
    public function setClient(string $client): static { $this->client = $client; return $this; }
    public function getNumeroClient(): ?string { return $this->numeroClient; }
    public function setNumeroClient(string $numeroClient): static { $this->numeroClient = $numeroClient; return $this; }
    public function getDateVente(): ?\DateTimeImmutable { return $this->dateVente; }
    public function setDateVente(\DateTimeImmutable $dateVente): static { $this->dateVente = $dateVente; return $this; }
    public function getMontantTotal(): float { return $this->montantTotal; }
    public function setMontantTotal(float $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }
    public function getPrixAdditionnel(): float { return $this->prixAdditionnel; }
    public function setPrixAdditionnel(float $prixAdditionnel): static { $this->prixAdditionnel = $prixAdditionnel; return $this; }

    /** @return Collection<int, Mouton> */
    public function getMoutons(): Collection { return $this->moutons; }
    public function addMouton(Mouton $mouton): static
    {
        if (!$this->moutons->contains($mouton)) { $this->moutons->add($mouton); }
        return $this;
    }
    public function removeMouton(Mouton $mouton): static
    {
        $this->moutons->removeElement($mouton);
        return $this;
    }
}
