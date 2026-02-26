<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $gardien = null;

    #[ORM\OneToOne(inversedBy: 'conversation')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Postulation $postulation = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getGardien(): ?User
    {
        return $this->gardien;
    }
    public function setGardien(?User $gardien): self
    {
        $this->gardien = $gardien;
        return $this;
    }

    public function getPostulation(): ?Postulation
    {
        return $this->postulation;
    }
    public function setPostulation(?Postulation $postulation): self
    {
        $this->postulation = $postulation;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}