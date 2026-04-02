<?php

namespace App\Entity;

use App\Repository\MoutonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MoutonRepository::class)]
class Mouton
{
    public const GENRE_MALE = 'Male';
    public const GENRE_FEMELLE = 'Femelle';
    public const ORIGINE_INTERNE = 'interne';
    public const ORIGINE_EXTERNE = 'externe';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $race = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::GENRE_MALE, self::GENRE_FEMELLE])]
    private ?string $genre = null;

    #[ORM\Column]
    #[Assert\Range(min: 0, max: 120)]
    private ?int $ageInitialMois = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $dateAjout = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private float $prix = 0.0;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: [self::ORIGINE_INTERNE, self::ORIGINE_EXTERNE])]
    private ?string $origine = null;

    #[ORM\Column]
    private bool $estVendu = false;

    #[ORM\ManyToOne(inversedBy: 'moutons')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Grange $grange = null;

    /** @var Collection<int, Vaccin> */
    #[ORM\OneToMany(mappedBy: 'mouton', targetEntity: Vaccin::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $vaccins;

    /** @var Collection<int, Infos> */
    #[ORM\OneToMany(mappedBy: 'mouton', targetEntity: Infos::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $infos;

    /** @var Collection<int, FactureVente> */
    #[ORM\ManyToMany(mappedBy: 'moutons', targetEntity: FactureVente::class)]
    private Collection $factureVentes;

    public function __construct()
    {
        $this->vaccins = new ArrayCollection();
        $this->infos = new ArrayCollection();
        $this->factureVentes = new ArrayCollection();
        $this->dateAjout = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getRace(): ?string { return $this->race; }
    public function setRace(string $race): static { $this->race = $race; return $this; }
    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(string $genre): static { $this->genre = $genre; return $this; }
    public function getAgeInitialMois(): ?int { return $this->ageInitialMois; }
    public function setAgeInitialMois(int $ageInitialMois): static { $this->ageInitialMois = $ageInitialMois; return $this; }
    public function getDateAjout(): ?\DateTimeImmutable { return $this->dateAjout; }
    public function setDateAjout(\DateTimeImmutable $dateAjout): static { $this->dateAjout = $dateAjout; return $this; }
    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $prix): static { $this->prix = $prix; return $this; }
    public function getOrigine(): ?string { return $this->origine; }
    public function setOrigine(string $origine): static { $this->origine = $origine; return $this; }
    public function isEstVendu(): bool { return $this->estVendu; }
    public function setEstVendu(bool $estVendu): static { $this->estVendu = $estVendu; return $this; }
    public function getGrange(): ?Grange { return $this->grange; }
    public function setGrange(?Grange $grange): static { $this->grange = $grange; return $this; }

    /** @return Collection<int, Vaccin> */
    public function getVaccins(): Collection { return $this->vaccins; }
    public function addVaccin(Vaccin $vaccin): static
    {
        if (!$this->vaccins->contains($vaccin)) { $this->vaccins->add($vaccin); $vaccin->setMouton($this); }
        return $this;
    }
    public function removeVaccin(Vaccin $vaccin): static
    {
        if ($this->vaccins->removeElement($vaccin) && $vaccin->getMouton() === $this) { $vaccin->setMouton(null); }
        return $this;
    }

    /** @return Collection<int, Infos> */
    public function getInfos(): Collection { return $this->infos; }
    public function addInfo(Infos $info): static
    {
        if (!$this->infos->contains($info)) { $this->infos->add($info); $info->setMouton($this); }
        return $this;
    }
    public function removeInfo(Infos $info): static
    {
        if ($this->infos->removeElement($info) && $info->getMouton() === $this) { $info->setMouton(null); }
        return $this;
    }

    public function getCurrentAge(): int
    {
        $initial = $this->ageInitialMois ?? 0;
        if (!$this->dateAjout) {
            return $initial;
        }

        $diff = $this->dateAjout->diff(new \DateTimeImmutable());
        $months = ($diff->y * 12) + $diff->m;

        return $initial + max(0, $months);
    }

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->id ?? 0, $this->race ?? '');
    }
}
