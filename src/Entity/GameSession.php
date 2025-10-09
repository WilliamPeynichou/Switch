<?php

namespace App\Entity;

use App\Repository\GameSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSessionRepository::class)]
class GameSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'gameSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\ManyToOne(targetEntity: Difficulty::class, inversedBy: 'gameSessions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Difficulty $difficulty = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'gameSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $masterUser = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionName = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $pausedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalDurationSeconds = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, SessionParticipant>
     */
    #[ORM\OneToMany(targetEntity: SessionParticipant::class, mappedBy: 'session')]
    private Collection $sessionParticipants;

    /**
     * @var Collection<int, GameEvent>
     */
    #[ORM\OneToMany(targetEntity: GameEvent::class, mappedBy: 'session')]
    private Collection $gameEvents;

    /**
     * @var Collection<int, SessionNote>
     */
    #[ORM\OneToMany(targetEntity: SessionNote::class, mappedBy: 'session')]
    private Collection $sessionNotes;

    /**
     * @var Collection<int, GameStatistic>
     */
    #[ORM\OneToMany(targetEntity: GameStatistic::class, mappedBy: 'session')]
    private Collection $gameStatistics;

    public function __construct()
    {
        $this->sessionParticipants = new ArrayCollection();
        $this->gameEvents = new ArrayCollection();
        $this->sessionNotes = new ArrayCollection();
        $this->gameStatistics = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): static
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getMasterUser(): ?User
    {
        return $this->masterUser;
    }

    public function setMasterUser(?User $masterUser): static
    {
        $this->masterUser = $masterUser;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getPausedAt(): ?\DateTimeImmutable
    {
        return $this->pausedAt;
    }

    public function setPausedAt(?\DateTimeImmutable $pausedAt): static
    {
        $this->pausedAt = $pausedAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getTotalDurationSeconds(): ?int
    {
        return $this->totalDurationSeconds;
    }

    public function setTotalDurationSeconds(?int $totalDurationSeconds): static
    {
        $this->totalDurationSeconds = $totalDurationSeconds;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
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
     * @return Collection<int, SessionParticipant>
     */
    public function getSessionParticipants(): Collection
    {
        return $this->sessionParticipants;
    }

    public function addSessionParticipant(SessionParticipant $sessionParticipant): static
    {
        if (!$this->sessionParticipants->contains($sessionParticipant)) {
            $this->sessionParticipants->add($sessionParticipant);
            $sessionParticipant->setSession($this);
        }
        return $this;
    }

    public function removeSessionParticipant(SessionParticipant $sessionParticipant): static
    {
        if ($this->sessionParticipants->removeElement($sessionParticipant)) {
            if ($sessionParticipant->getSession() === $this) {
                $sessionParticipant->setSession(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, GameEvent>
     */
    public function getGameEvents(): Collection
    {
        return $this->gameEvents;
    }

    public function addGameEvent(GameEvent $gameEvent): static
    {
        if (!$this->gameEvents->contains($gameEvent)) {
            $this->gameEvents->add($gameEvent);
            $gameEvent->setSession($this);
        }
        return $this;
    }

    public function removeGameEvent(GameEvent $gameEvent): static
    {
        if ($this->gameEvents->removeElement($gameEvent)) {
            if ($gameEvent->getSession() === $this) {
                $gameEvent->setSession(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, SessionNote>
     */
    public function getSessionNotes(): Collection
    {
        return $this->sessionNotes;
    }

    public function addSessionNote(SessionNote $sessionNote): static
    {
        if (!$this->sessionNotes->contains($sessionNote)) {
            $this->sessionNotes->add($sessionNote);
            $sessionNote->setSession($this);
        }
        return $this;
    }

    public function removeSessionNote(SessionNote $sessionNote): static
    {
        if ($this->sessionNotes->removeElement($sessionNote)) {
            if ($sessionNote->getSession() === $this) {
                $sessionNote->setSession(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, GameStatistic>
     */
    public function getGameStatistics(): Collection
    {
        return $this->gameStatistics;
    }

    public function addGameStatistic(GameStatistic $gameStatistic): static
    {
        if (!$this->gameStatistics->contains($gameStatistic)) {
            $this->gameStatistics->add($gameStatistic);
            $gameStatistic->setSession($this);
        }
        return $this;
    }

    public function removeGameStatistic(GameStatistic $gameStatistic): static
    {
        if ($this->gameStatistics->removeElement($gameStatistic)) {
            if ($gameStatistic->getSession() === $this) {
                $gameStatistic->setSession(null);
            }
        }
        return $this;
    }
}
