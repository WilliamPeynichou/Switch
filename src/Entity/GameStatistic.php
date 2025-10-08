<?php

namespace App\Entity;

use App\Repository\GameStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameStatisticRepository::class)]
class GameStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameSession::class, inversedBy: 'gameStatistics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSession $session = null;

    #[ORM\ManyToOne(targetEntity: SessionParticipant::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionParticipant $player = null;

    #[ORM\Column]
    private ?int $shotsAttempted = 0;

    #[ORM\Column]
    private ?int $shotsMade = 0;

    #[ORM\Column]
    private ?int $airballsCount = 0;

    #[ORM\Column]
    private ?int $bricksCount = 0;

    #[ORM\Column]
    private ?int $positionsAdvanced = 0;

    #[ORM\Column]
    private ?int $positionsLost = 0;

    #[ORM\Column]
    private ?int $timePlayedSeconds = 0;

    #[ORM\Column(nullable: true)]
    private ?int $finalPositionReached = null;

    #[ORM\Column]
    private ?int $totalPoints = 0;

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

    public function getAirballsCount(): ?int
    {
        return $this->airballsCount;
    }

    public function setAirballsCount(int $airballsCount): static
    {
        $this->airballsCount = $airballsCount;
        return $this;
    }

    public function getBricksCount(): ?int
    {
        return $this->bricksCount;
    }

    public function setBricksCount(int $bricksCount): static
    {
        $this->bricksCount = $bricksCount;
        return $this;
    }

    public function getPositionsAdvanced(): ?int
    {
        return $this->positionsAdvanced;
    }

    public function setPositionsAdvanced(int $positionsAdvanced): static
    {
        $this->positionsAdvanced = $positionsAdvanced;
        return $this;
    }

    public function getPositionsLost(): ?int
    {
        return $this->positionsLost;
    }

    public function setPositionsLost(int $positionsLost): static
    {
        $this->positionsLost = $positionsLost;
        return $this;
    }

    public function getTimePlayedSeconds(): ?int
    {
        return $this->timePlayedSeconds;
    }

    public function setTimePlayedSeconds(int $timePlayedSeconds): static
    {
        $this->timePlayedSeconds = $timePlayedSeconds;
        return $this;
    }

    public function getFinalPositionReached(): ?int
    {
        return $this->finalPositionReached;
    }

    public function setFinalPositionReached(?int $finalPositionReached): static
    {
        $this->finalPositionReached = $finalPositionReached;
        return $this;
    }

    public function getTotalPoints(): ?int
    {
        return $this->totalPoints;
    }

    public function setTotalPoints(int $totalPoints): static
    {
        $this->totalPoints = $totalPoints;
        return $this;
    }
}
