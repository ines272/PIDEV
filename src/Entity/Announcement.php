<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TypeGuard;


#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?float $altitude = null;

    #[ORM\Column(enumType: TypeGuard::class)]
    private ?TypeGuard $careType = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column]
    private ?int $visitPerDay = null;

    #[ORM\Column]
    private ?float $renumerationMin = null;

    #[ORM\Column]
    private ?float $renumerationMax = null;

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

    public function getCareType(): ?TypeGuard
    {
        return $this->careType;
    }

    public function setCareType(?TypeGuard $careType): static
    {
        $this->careType = $careType;

        return $this;
    }


    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getVisitPerDay(): ?int
    {
        return $this->visitPerDay;
    }

    public function setVisitPerDay(int $visitPerDay): static
    {
        $this->visitPerDay = $visitPerDay;

        return $this;
    }

    public function getRenumerationMin(): ?float
    {
        return $this->renumerationMin;
    }

    public function setRenumerationMin(float $renumerationMin): static
    {
        $this->renumerationMin = $renumerationMin;

        return $this;
    }

    public function getRenumerationMax(): ?float
    {
        return $this->renumerationMax;
    }

    public function setRenumerationMax(float $renumerationMax): static
    {
        $this->renumerationMax = $renumerationMax;

        return $this;
    }
}
