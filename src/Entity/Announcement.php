<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: 'float')]
    private ?float $longitude = null;

    #[ORM\Column(type: 'float')]
    private ?float $altitude = null;

    #[ORM\Column(length: 255)]
    private ?string $care_type = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: 'integer')]
    private ?int $visit_per_day = null;

    #[ORM\Column(type: 'float')]
    private ?float $renumeration_min = null;

    #[ORM\Column(type: 'float')]
    private ?float $renumeration_max = null;

    // ============ RELATIONS ============
    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pet $pet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function setAltitude(float $altitude): static
    {
        $this->altitude = $altitude;
        return $this;
    }

    public function getCareType(): ?string
    {
        return $this->care_type;
    }

    public function setCareType(string $care_type): static
    {
        $this->care_type = $care_type;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getVisitPerDay(): ?int
    {
        return $this->visit_per_day;
    }

    public function setVisitPerDay(int $visit_per_day): static
    {
        $this->visit_per_day = $visit_per_day;
        return $this;
    }

    public function getRenumerationMin(): ?float
    {
        return $this->renumeration_min;
    }

    public function setRenumerationMin(float $renumeration_min): static
    {
        $this->renumeration_min = $renumeration_min;
        return $this;
    }

    public function getRenumerationMax(): ?float
    {
        return $this->renumeration_max;
    }

    public function setRenumerationMax(float $renumeration_max): static
    {
        $this->renumeration_max = $renumeration_max;
        return $this;
    }

    // ============ GETTERS/SETTERS POUR LES RELATIONS ============
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(?Pet $pet): static
    {
        $this->pet = $pet;
        return $this;
    }
}