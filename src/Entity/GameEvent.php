<?php

namespace App\Entity;

use App\Repository\GameEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameEventRepository::class)]
class GameEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameSession::class, inversedBy: 'gameEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSession $session = null;

    #[ORM\ManyToOne(targetEntity: SessionParticipant::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionParticipant $player = null;

    #[ORM\Column(length: 50)]
    private ?string $eventType = null;

    #[ORM\Column(nullable: true)]
    private ?int $fromPosition = null;

    #[ORM\Column(nullable: true)]
    private ?int $toPosition = null;

    #[ORM\Column(nullable: true)]
    private ?int $pointsScored = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $timestampSeconds = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getPlayer(): ?SessionParticipant
    {
        return $this->player;
    }

    public function setPlayer(?SessionParticipant $player): static
    {
        $this->player = $player;
        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): static
    {
        $this->eventType = $eventType;
        return $this;
    }

    public function getFromPosition(): ?int
    {
        return $this->fromPosition;
    }

    public function setFromPosition(?int $fromPosition): static
    {
        $this->fromPosition = $fromPosition;
        return $this;
    }

    public function getToPosition(): ?int
    {
        return $this->toPosition;
    }

    public function setToPosition(?int $toPosition): static
    {
        $this->toPosition = $toPosition;
        return $this;
    }

    public function getPointsScored(): ?int
    {
        return $this->pointsScored;
    }

    public function setPointsScored(?int $pointsScored): static
    {
        $this->pointsScored = $pointsScored;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getTimestampSeconds(): ?int
    {
        return $this->timestampSeconds;
    }

    public function setTimestampSeconds(?int $timestampSeconds): static
    {
        $this->timestampSeconds = $timestampSeconds;
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
}
