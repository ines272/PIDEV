<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
#[Assert\Length(
    min: 3,
    minMessage: "Le nom doit contenir au moins {{ limit }} caractères."
)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
#[Assert\GreaterThanOrEqual(
    value: "today",
    message: "La date doit être aujourd’hui ou dans le futur."
)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'heure est obligatoire.")]
#[Assert\Regex(
    pattern: "/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/",
    message: "Format invalide. Utilisez HH:MM (ex: 14:30)."
)]
    private ?string $heure = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
#[Assert\Length(
    min: 5,
    minMessage: "L'adresse est trop courte."
)]
    private ?string $addresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
#[Assert\Length(
    min: 5,
    minMessage: "La description doit contenir au moins {{ limit }} caractères."
)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): static
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
