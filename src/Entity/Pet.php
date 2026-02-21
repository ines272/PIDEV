<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\Gender;
use App\Enum\PetType;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(min: 2, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.")]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThan("today", message: "La date doit être dans le passé.")]
    private ?\DateTime $birthDate = null;

    #[ORM\Column(enumType: PetType::class)]
    #[Assert\NotNull(message: "Veuillez sélectionner un type d'animal.")]
    private ?PetType $typePet = null;



    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La race est obligatoire.")]
    private ?string $breed = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le poids est obligatoire.")]
    #[Assert\Positive(message: "Le poids doit être positif.")]
    private ?float $weight = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 5, minMessage: "La description doit contenir au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[ORM\Column(enumType: Gender::class)]
    #[Assert\NotNull(message: "Veuillez sélectionner un genre.")]
    private ?Gender $gender = null;

    #[ORM\Column]
    private ?bool $hasContagiousDisease = null;

    #[ORM\Column]
    private ?bool $hasMedicalRecord = null;

    #[ORM\Column]
    private ?bool $hasCriticalCondition = null;

    #[ORM\Column]
    private ?bool $isVaccinated = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

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

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getTypePet(): ?PetType
    {
        return $this->typePet;
    }

    public function setTypePet(?PetType $typePet): static
    {
        $this->typePet = $typePet;
        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }


    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

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

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    public function isVaccinated(): ?bool
    {
        return $this->isVaccinated;
    }

    public function setIsVaccinated(bool $isVaccinated): static
    {
        $this->isVaccinated = $isVaccinated;

        return $this;
    }

    public function hasContagiousDisease(): ?bool
    {
        return $this->hasContagiousDisease;
    }

    public function setHasContagiousDisease(bool $hasContagiousDisease): static
    {
        $this->hasContagiousDisease = $hasContagiousDisease;

        return $this;
    }

    public function hasMedicalRecord(): ?bool
    {
        return $this->hasMedicalRecord;
    }

    public function setHasMedicalRecord(bool $hasMedicalRecord): static
    {
        $this->hasMedicalRecord = $hasMedicalRecord;

        return $this;
    }

    public function hasCriticalCondition(): ?bool
    {
        return $this->hasCriticalCondition;
    }

    public function setHasCriticalCondition(bool $hasCriticalCondition): static
    {
        $this->hasCriticalCondition = $hasCriticalCondition;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }
}
