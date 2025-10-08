<?php

namespace App\Entity;

use App\Repository\SessionParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionParticipantRepository::class)]
class SessionParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameSession::class, inversedBy: 'sessionParticipants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSession $session = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sessionParticipants')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $playerName = null;

    #[ORM\Column]
    private bool $isAnonymous = false;

    #[ORM\Column]
    private ?int $playerNumber = null;

    #[ORM\Column]
    private ?int $currentPosition = 1;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $leftAt = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPlayerName(): ?string
    {
        return $this->playerName;
    }

    public function setPlayerName(string $playerName): static
    {
        $this->playerName = $playerName;
        return $this;
    }

    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    public function setAnonymous(bool $isAnonymous): static
    {
        $this->isAnonymous = $isAnonymous;
        return $this;
    }

    public function getPlayerNumber(): ?int
    {
        return $this->playerNumber;
    }

    public function setPlayerNumber(int $playerNumber): static
    {
        $this->playerNumber = $playerNumber;
        return $this;
    }

    public function getCurrentPosition(): ?int
    {
        return $this->currentPosition;
    }

    public function setCurrentPosition(int $currentPosition): static
    {
        $this->currentPosition = $currentPosition;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }

    public function getLeftAt(): ?\DateTimeImmutable
    {
        return $this->leftAt;
    }

    public function setLeftAt(?\DateTimeImmutable $leftAt): static
    {
        $this->leftAt = $leftAt;
        return $this;
    }
}
