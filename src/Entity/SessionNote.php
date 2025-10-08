<?php

namespace App\Entity;

use App\Repository\SessionNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionNoteRepository::class)]
class SessionNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameSession::class, inversedBy: 'sessionNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSession $session = null;

    #[ORM\Column(type: 'text')]
    private ?string $noteText = null;

    #[ORM\Column(length: 50)]
    private ?string $noteType = 'general';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?GameSession
    {
        return $this->session;
    }

    public function setSession(?GameSession $session): static
    {
        $this->session = $session;
        return $this;
    }

    public function getNoteText(): ?string
    {
        return $this->noteText;
    }

    public function setNoteText(string $noteText): static
    {
        $this->noteText = $noteText;
        return $this;
    }

    public function getNoteType(): ?string
    {
        return $this->noteType;
    }

    public function setNoteType(string $noteType): static
    {
        $this->noteType = $noteType;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
