<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'animal est obligatoire")]
    private ?string $name = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire")]
    private ?\DateTimeInterface $birth_date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type d'animal est obligatoire")]
    private ?string $type_pet = null;

    #[ORM\Column(length: 255)]
    private ?string $breed = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive(message: "Le poids doit être positif")]
    private ?float $weight = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $is_vaccinated = false;

    #[ORM\Column(type: 'boolean')]
    private ?bool $has_contagious_disease = false;

    #[ORM\Column(type: 'boolean')]
    private ?bool $has_medical_record = false;

    #[ORM\Column(type: 'boolean')]
    private ?bool $has_critical_condition = false;

    // ============ RELATION AVEC USER ============
    #[ORM\ManyToOne(inversedBy: 'pets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): static
    {
        $this->birth_date = $birth_date;
        return $this;
    }

    public function getTypePet(): ?string
    {
        return $this->type_pet;
    }

    public function setTypePet(string $type_pet): static
    {
        $this->type_pet = $type_pet;
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    public function isVaccinated(): ?bool
    {
        return $this->is_vaccinated;
    }

    public function setIsVaccinated(bool $is_vaccinated): static
    {
        $this->is_vaccinated = $is_vaccinated;
        return $this;
    }

    public function hasContagiousDisease(): ?bool
    {
        return $this->has_contagious_disease;
    }

    public function setHasContagiousDisease(bool $has_contagious_disease): static
    {
        $this->has_contagious_disease = $has_contagious_disease;
        return $this;
    }

    public function hasMedicalRecord(): ?bool
    {
        return $this->has_medical_record;
    }

    public function setHasMedicalRecord(bool $has_medical_record): static
    {
        $this->has_medical_record = $has_medical_record;
        return $this;
    }

    public function hasCriticalCondition(): ?bool
    {
        return $this->has_critical_condition;
    }

    public function setHasCriticalCondition(bool $has_critical_condition): static
    {
        $this->has_critical_condition = $has_critical_condition;
        return $this;
    }

    // ============ GETTERS/SETTERS POUR LA RELATION ============
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}