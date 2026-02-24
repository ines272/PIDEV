<?php

namespace App\Entity;

use App\Repository\PostulationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostulationRepository::class)]
class Postulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 👤 The user who applies (Gardien)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $applicant = null;

    // 📢 The announcement
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Announcement $announcement = null;

    // 🏠 Owner of the announcement
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 20)]
    private string $status = 'PENDING';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'PENDING';
    }

    // =============================
    // GETTERS & SETTERS
    // =============================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;
        return $this;
    }

    public function getAnnouncement(): ?Announcement
    {
        return $this->announcement;
    }

    public function setAnnouncement(?Announcement $announcement): static
    {
        $this->announcement = $announcement;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // 💬 Conversation created after acceptance
#[ORM\OneToOne(mappedBy: 'postulation', targetEntity: Conversation::class)]
private ?Conversation $conversation = null;

public function getConversation(): ?Conversation
{
    return $this->conversation;
}

public function setConversation(?Conversation $conversation): static
{
    $this->conversation = $conversation;
    return $this;
}

    // =============================
    // Helper Methods (Optional but Useful)
    // =============================

    public function accept(): void
    {
        $this->status = 'ACCEPTED';
    }

    public function reject(): void
    {
        $this->status = 'REJECTED';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'ACCEPTED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }
}