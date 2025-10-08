<?php

namespace App\Entity;

use App\Repository\GameHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameHistoryRepository::class)]
#[ORM\Table(name: 'game_history')]
class GameHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionName = null;

    #[ORM\Column]
    private ?int $totalParticipants = 0;

    #[ORM\Column]
    private ?int $finalistId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $finalistName = null;

    #[ORM\Column]
    private ?int $winnerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $winnerName = null;

    #[ORM\Column]
    private ?int $totalDurationSeconds = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $gameNotes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;
        return $this;
    }

    public function getSessionName(): ?string
    {
        return $this->sessionName;
    }

    public function setSessionName(string $sessionName): static
    {
        $this->sessionName = $sessionName;
        return $this;
    }

    public function getTotalParticipants(): ?int
    {
        return $this->totalParticipants;
    }

    public function setTotalParticipants(int $totalParticipants): static
    {
        $this->totalParticipants = $totalParticipants;
        return $this;
    }

    public function getFinalistId(): ?int
    {
        return $this->finalistId;
    }

    public function setFinalistId(?int $finalistId): static
    {
        $this->finalistId = $finalistId;
        return $this;
    }

    public function getFinalistName(): ?string
    {
        return $this->finalistName;
    }

    public function setFinalistName(?string $finalistName): static
    {
        $this->finalistName = $finalistName;
        return $this;
    }

    public function getWinnerId(): ?int
    {
        return $this->winnerId;
    }

    public function setWinnerId(?int $winnerId): static
    {
        $this->winnerId = $winnerId;
        return $this;
    }

    public function getWinnerName(): ?string
    {
        return $this->winnerName;
    }

    public function setWinnerName(?string $winnerName): static
    {
        $this->winnerName = $winnerName;
        return $this;
    }

    public function getTotalDurationSeconds(): ?int
    {
        return $this->totalDurationSeconds;
    }

    public function setTotalDurationSeconds(int $totalDurationSeconds): static
    {
        $this->totalDurationSeconds = $totalDurationSeconds;
        return $this;
    }

    public function getGameNotes(): ?string
    {
        return $this->gameNotes;
    }

    public function setGameNotes(?string $gameNotes): static
    {
        $this->gameNotes = $gameNotes;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
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

    /**
     * Calcule la durée de la partie en minutes
     */
    public function getDurationMinutes(): float
    {
        if (!$this->startedAt || !$this->completedAt) {
            return 0;
        }
        
        $duration = $this->completedAt->getTimestamp() - $this->startedAt->getTimestamp();
        return round($duration / 60, 1);
    }

    /**
     * Formate la durée pour l'affichage
     */
    public function getFormattedDuration(): string
    {
        $minutes = $this->getDurationMinutes();
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        return $hours . 'h ' . $remainingMinutes . 'min';
    }
}
