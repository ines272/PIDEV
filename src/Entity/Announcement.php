<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TypeGuard;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(min: 5, minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.")]
    private ?string $address = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La longitude est obligatoire.")]
    #[Assert\Range(
        min: -180,
        max: 180,
        notInRangeMessage: "La longitude doit être entre -180 et 180."
    )]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'altitude est obligatoire.")]
    private ?float $altitude = null;

    #[ORM\Column(enumType: TypeGuard::class)]
    #[Assert\NotNull(message: "Veuillez sélectionner un type de garde.")]
    private ?TypeGuard $careType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date début doit être aujourd'hui ou future.")]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    private ?\DateTime $dateFin = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de visites est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de visites doit être positif.")]
    private ?int $visitPerDay = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La rémunération minimale est obligatoire.")]
    #[Assert\Positive(message: "La rémunération minimale doit être positive.")]
    private ?float $renumerationMin = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La rémunération maximale est obligatoire.")]
    #[Assert\Positive(message: "La rémunération maximale doit être positive.")]
    private ?float $renumerationMax = null;

    /**
     * VALIDATION PERSONNALISÉE
     */
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->dateDebut && $this->dateFin) {
            if ($this->dateFin < $this->dateDebut) {
                $context->buildViolation("La date de fin doit être après la date de début.")
                    ->atPath('dateFin')
                    ->addViolation();
            }
        }

        if ($this->renumerationMin && $this->renumerationMax) {
            if ($this->renumerationMax < $this->renumerationMin) {
                $context->buildViolation("La rémunération maximale doit être supérieure à la minimale.")
                    ->atPath('renumerationMax')
                    ->addViolation();
            }
        }
    }

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

    public function setDateDebut(?\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): static
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
