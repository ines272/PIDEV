<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TypeGuard;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\Pet;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{

    public function __construct()
    {
        $this->postulations = new ArrayCollection();
    }
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Choisissez un animal.")]
    private ?Pet $pet = null;

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(?Pet $pet): static
    {
        $this->pet = $pet;
        return $this;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(min: 5, minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.")]
    private ?string $address = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $visitHours = [];

    #[ORM\Column(enumType: TypeGuard::class)]
    #[Assert\NotNull(message: "Veuillez sélectionner un type de garde.")]
    private ?TypeGuard $careType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date début doit être aujourd'hui ou future.")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(nullable: true)]
    // #[Assert\NotBlank(message: "Le nombre de visites est obligatoire.")]
    private ?int $visitPerDay = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La rémunération minimale est obligatoire.")]
    #[Assert\Positive(message: "La rémunération minimale doit être positive.")]
    private ?float $renumerationMin = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "La rémunération maximale est obligatoire.")]

    #[Assert\Positive(message: "La rémunération maximale doit être positive.")]
    private ?float $renumerationMax = null;

    #[ORM\OneToMany(mappedBy: 'announcement', targetEntity: Postulation::class, cascade: ['remove'])]
    private Collection $postulations;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le service est obligatoire.")]
    private ?string $services = null;




    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * VALIDATION PERSONNALISÉE FUSIONNÉE
     */
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        // Validation dates
        if ($this->dateDebut && $this->dateFin) {
            if ($this->dateFin < $this->dateDebut) {
                $context->buildViolation("La date de fin doit être après la date de début.")
                    ->atPath('dateFin')
                    ->addViolation();
            }
        }

        // Validation rémunération
        if ($this->renumerationMin !== null && $this->renumerationMax !== null) {
            if ($this->renumerationMax < $this->renumerationMin) {
                $context->buildViolation("La rémunération maximale doit être supérieure à la minimale.")
                    ->atPath('renumerationMax')
                    ->addViolation();
            }
        }

        // Validation spécifique CHEZ_MOI
        if ($this->careType === TypeGuard::CHEZ_MOI) {
            if (!$this->visitPerDay || $this->visitPerDay <= 0) {
                $context->buildViolation("Le nombre de visites est obligatoire pour la garde chez moi.")
                    ->atPath('visitPerDay')
                    ->addViolation();
            }

            // if (empty($this->visitHours)) {
            //     $context->buildViolation("Veuillez ajouter au moins un horaire de visite.")
            //         ->atPath('visitHours')
            //         ->addViolation();
            // }
        }
    }

    // ======== GETTERS & SETTERS ========
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

    public function getVisitHours(): ?array
    {
        return $this->visitHours;
    }

    public function setVisitHours(?array $visitHours): static
    {
        $this->visitHours = $visitHours;
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

    public function setVisitPerDay(?int $visitPerDay): static
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

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(?string $services): static
    {
        $this->services = $services;
        return $this;
    }

    /**
     * @return Collection<int, Postulation>
     */
    public function getPostulations(): Collection
    {
        return $this->postulations;
    }
}