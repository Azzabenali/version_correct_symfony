<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[ORM\Column(length: 255)]
    private string $name;


    #[ORM\Column]
    private bool $isVerified = false;

   #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
private Collection $reservations;





    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Identifiant Symfony (MODERNE)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * ⚠️ COMPATIBILITÉ UNIQUEMENT
     * username = name s’il existe, sinon email
     * ❌ PAS de champ username
     */
   
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getName(): string
{
    return $this->name;
}

public function setName(string $name): self
{
    $this->name = $name;
    return $this;
}

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

   
    public function getReservations(): Collection
{
    return $this->reservations;
}

// Ajouter / supprimer réservation
public function addReservation(Reservation $reservation): self
{
    if (!$this->reservations->contains($reservation)) {
        $this->reservations->add($reservation);
        $reservation->setUser($this);
    }

    return $this;
}

public function removeReservation(Reservation $reservation): self
{
    if ($this->reservations->removeElement($reservation)) {
        if ($reservation->getUser() === $this) {
            $reservation->setUser(null);
        }
    }

    return $this;
}
}
