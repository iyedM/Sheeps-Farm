<?php

namespace App\Aid\Entity;

use App\Aid\Repository\AidMarcheRepository;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AidMarcheRepository::class)]
#[ORM\Table(name: 'aid_marche')]
class AidMarche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(name: 'moutons_amenes', options: ['default' => 0])]
    private int $moutonsAmenesDb = 0;

    #[ORM\Column(name: 'moutons_vendus', options: ['default' => 0])]
    private int $moutonsVendusDb = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?string $reduction = null;

    #[ORM\ManyToOne(inversedBy: 'marches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?AidCampagne $campagne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $responsable = null;

    /** @var Collection<int, AidMarcheLigne> */
    #[ORM\OneToMany(mappedBy: 'marche', targetEntity: AidMarcheLigne::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
        $this->lignes = new ArrayCollection();
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMoutonsVendus(): int
    {
        if ($this->lignes->isEmpty()) {
            return $this->moutonsVendusDb;
        }

        $total = 0;

        foreach ($this->lignes as $ligne) {
            $total += $ligne->getQuantiteVendus();
        }

        return $total;
    }

    public function getMoutonsAmenes(): int
    {
        if ($this->lignes->isEmpty()) {
            return $this->moutonsAmenesDb;
        }

        $total = 0;

        foreach ($this->lignes as $ligne) {
            $total += $ligne->getQuantiteAmenes();
        }

        return $total;
    }

    public function getReduction(): ?string
    {
        return $this->reduction;
    }

    public function setReduction(?string $reduction): static
    {
        $this->reduction = $reduction !== null && $reduction !== '' ? number_format((float) $reduction, 2, '.', '') : null;

        return $this;
    }

    public function getReductionValue(): float
    {
        return (float) ($this->reduction ?? 0);
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

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    /** @return Collection<int, AidMarcheLigne> */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(AidMarcheLigne $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setMarche($this);
        }

        return $this;
    }

    public function removeLigne(AidMarcheLigne $ligne): static
    {
        if ($this->lignes->removeElement($ligne) && $ligne->getMarche() === $this) {
            $ligne->setMarche(null);
        }

        return $this;
    }

    public function recalculateTotals(): static
    {
        $this->moutonsAmenesDb = 0;
        $this->moutonsVendusDb = 0;

        foreach ($this->lignes as $ligne) {
            $this->moutonsAmenesDb += $ligne->getQuantiteAmenes();
            $this->moutonsVendusDb += $ligne->getQuantiteVendus();
        }

        return $this;
    }

    public function getTotalNet(): float
    {
        $total = 0.0;

        foreach ($this->lignes as $ligne) {
            $total += $ligne->getMontantLigne();
        }

        return $total - $this->getReductionValue();
    }
}
