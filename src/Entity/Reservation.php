<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $resdate = null;

    #[ORM\Column]
    private ?int $numbertickets = null;

    #[ORM\Column]
    private ?float $totalprice = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToMany(
    mappedBy: 'reservation',
    targetEntity: Ticket::class,
    cascade: ['persist'],
    orphanRemoval: true
)]
private Collection $tickets; // ← note le "s" à la fin


    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResdate(): ?\DateTime
    {
        return $this->resdate;
    }

    public function setResdate(\DateTime $resdate): static
    {
        $this->resdate = $resdate;
        return $this;
    }

    public function getNumbertickets(): ?int
    {
        return $this->numbertickets;
    }

    public function setNumbertickets(int $numbertickets): static
    {
        $this->numbertickets = $numbertickets;
        return $this;
    }

    public function getTotalprice(): ?float
    {
        return $this->totalprice;
    }

    public function setTotalprice(float $totalprice): static
    {
        $this->totalprice = $totalprice;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
{
    return $this->tickets;
}

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setReservation($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getReservation() === $this) {
                $ticket->setReservation(null);
            }
        }

        return $this;
    }
}
