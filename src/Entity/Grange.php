<?php

namespace App\Entity;

use App\Repository\GrangeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GrangeRepository::class)]
class Grange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $localisation = null;

    /** @var Collection<int, Mouton> */
    #[ORM\OneToMany(mappedBy: 'grange', targetEntity: Mouton::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $moutons;

    public function __construct()
    {
        $this->moutons = new ArrayCollection();
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
        $this->nom = $nom;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    /** @return Collection<int, Mouton> */
    public function getMoutons(): Collection
    {
        return $this->moutons;
    }

    public function addMouton(Mouton $mouton): static
    {
        if (!$this->moutons->contains($mouton)) {
            $this->moutons->add($mouton);
            $mouton->setGrange($this);
        }

        return $this;
    }

    public function removeMouton(Mouton $mouton): static
    {
        if ($this->moutons->removeElement($mouton) && $mouton->getGrange() === $this) {
            $mouton->setGrange(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
}
