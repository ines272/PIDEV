<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: "notification")]
#[ORM\Index(columns: ["user_id"])]
#[ORM\Index(columns: ["is_read"])]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    // =====================================================
    // ID
    // =====================================================

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    // =====================================================
    // RELATION
    // =====================================================

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "notifications")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    // =====================================================
    // CONTENT
    // =====================================================

    #[ORM\Column(length: 255)]
    #[Groups(['notification:read'])]
    private ?string $title = null;

    #[ORM\Column(type: "text")]
    #[Groups(['notification:read'])]
    private ?string $message = null;

    #[ORM\Column(length: 50)]
    #[Groups(['notification:read'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification:read'])]
    private ?string $actionUrl = null;

    #[ORM\Column(length: 20)]
    #[Groups(['notification:read'])]
    private string $priority = 'normal';

    // =====================================================
    // STATE
    // =====================================================

    #[ORM\Column(type: "boolean")]
    #[Groups(['notification:read'])]
    private bool $isRead = false;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    // =====================================================
    // TIMESTAMPS
    // =====================================================

    #[ORM\Column(type: "datetime_immutable")]
    #[Groups(['notification:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // =====================================================
    // CONSTRUCTOR
    // =====================================================

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isRead = false;
        $this->priority = 'normal';
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // =====================================================
    // BUSINESS METHODS
    // =====================================================

    public function markAsRead(): self
    {
        $this->isRead = true;
        $this->readAt = new \DateTimeImmutable();

        return $this;
    }

    // =====================================================
    // GETTERS & SETTERS
    // =====================================================

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }

    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getMessage(): ?string { return $this->message; }

    public function setMessage(string $message): self { $this->message = $message; return $this; }

    public function getType(): ?string { return $this->type; }

    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getActionUrl(): ?string { return $this->actionUrl; }

    public function setActionUrl(?string $actionUrl): self { $this->actionUrl = $actionUrl; return $this; }

    public function getPriority(): string { return $this->priority; }

    public function setPriority(string $priority): self { $this->priority = $priority; return $this; }

    public function isRead(): bool { return $this->isRead; }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        if ($isRead && $this->readAt === null) {
            $this->readAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}