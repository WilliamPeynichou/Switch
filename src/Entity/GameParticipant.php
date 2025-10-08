<?php

namespace App\Entity;

use App\Repository\GameParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameParticipantRepository::class)]
#[ORM\Table(name: 'game_participants')]
class GameParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $playerName = null;

    #[ORM\Column]
    private ?int $playerNumber = null;

    #[ORM\Column]
    private ?int $currentPosition = 0;

    #[ORM\Column]
    private ?int $score = 0;

    #[ORM\Column]
    private ?int $shotsAttempted = 0;

    #[ORM\Column]
    private ?int $shotsMade = 0;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?bool $isEliminated = false;

    #[ORM\Column]
    private ?bool $isFinalist = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $eliminatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getShotsAttempted(): ?int
    {
        return $this->shotsAttempted;
    }

    public function setShotsAttempted(int $shotsAttempted): static
    {
        $this->shotsAttempted = $shotsAttempted;
        return $this;
    }

    public function getShotsMade(): ?int
    {
        return $this->shotsMade;
    }

    public function setShotsMade(int $shotsMade): static
    {
        $this->shotsMade = $shotsMade;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isEliminated(): ?bool
    {
        return $this->isEliminated;
    }

    public function setEliminated(bool $isEliminated): static
    {
        $this->isEliminated = $isEliminated;
        if ($isEliminated) {
            $this->eliminatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function isFinalist(): ?bool
    {
        return $this->isFinalist;
    }

    public function setFinalist(bool $isFinalist): static
    {
        $this->isFinalist = $isFinalist;
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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getEliminatedAt(): ?\DateTimeImmutable
    {
        return $this->eliminatedAt;
    }

    public function setEliminatedAt(?\DateTimeImmutable $eliminatedAt): static
    {
        $this->eliminatedAt = $eliminatedAt;
        return $this;
    }

    /**
     * Calcule le pourcentage de réussite
     */
    public function getSuccessRate(): float
    {
        if ($this->shotsAttempted === 0) {
            return 0.0;
        }
        return round(($this->shotsMade / $this->shotsAttempted) * 100, 1);
    }

    /**
     * Ajoute un tir tenté
     */
    public function addShotAttempt(bool $made = false): static
    {
        $this->shotsAttempted++;
        if ($made) {
            $this->shotsMade++;
        }
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * Avance d'une position
     */
    public function advancePosition(): static
    {
        $this->currentPosition++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
