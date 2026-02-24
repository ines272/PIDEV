<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Conversation $conversation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\Column(type: 'text')]
    private string $content;

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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }
    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }
    public function setSender(?User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}