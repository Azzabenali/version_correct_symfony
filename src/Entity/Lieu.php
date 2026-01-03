<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'lieu')]
    private Collection $events;

    /**
     * @var Collection<int, Conference>
     */
    #[ORM\OneToMany(targetEntity: Conference::class, mappedBy: 'lieu')]
    private Collection $conferences;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->conferences = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setLieu($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getLieu() === $this) {
                $event->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conference>
     */
    public function getConferences(): Collection
    {
        return $this->conferences;
    }

    public function addConference(Conference $conference): static
    {
        if (!$this->conferences->contains($conference)) {
            $this->conferences->add($conference);
            $conference->setLieu($this);
        }

        return $this;
    }

    public function removeConference(Conference $conference): static
    {
        if ($this->conferences->removeElement($conference)) {
            // set the owning side to null (unless already changed)
            if ($conference->getLieu() === $this) {
                $conference->setLieu(null);
            }
        }

        return $this;
    }
public function __toString(): string
    {
        return $this->nom ?? 'Lieu';
    }


}
