<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private ?string $baseRules = null;

    #[ORM\Column]
    private ?int $minPlayers = null;

    #[ORM\Column]
    private ?int $maxPlayers = null;

    #[ORM\Column]
    private ?int $defaultDurationMinutes = null;

    #[ORM\Column(length: 50)]
    private ?string $gameType = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $createdByUser = null;

    #[ORM\Column]
    private bool $isOfficial = true;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isActive = true;

    /**
     * @var Collection<int, GamePosition>
     */
    #[ORM\OneToMany(targetEntity: GamePosition::class, mappedBy: 'game', cascade: ['persist', 'remove'])]
    private Collection $gamePositions;

    /**
     * @var Collection<int, GameSession>
     */
    #[ORM\OneToMany(targetEntity: GameSession::class, mappedBy: 'game')]
    private Collection $gameSessions;

    public function __construct()
    {
        $this->gamePositions = new ArrayCollection();
        $this->gameSessions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getBaseRules(): ?string
    {
        return $this->baseRules;
    }

    public function setBaseRules(string $baseRules): static
    {
        $this->baseRules = $baseRules;
        return $this;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(int $minPlayers): static
    {
        $this->minPlayers = $minPlayers;
        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;
        return $this;
    }

    public function getDefaultDurationMinutes(): ?int
    {
        return $this->defaultDurationMinutes;
    }

    public function setDefaultDurationMinutes(int $defaultDurationMinutes): static
    {
        $this->defaultDurationMinutes = $defaultDurationMinutes;
        return $this;
    }

    public function getGameType(): ?string
    {
        return $this->gameType;
    }

    public function setGameType(string $gameType): static
    {
        $this->gameType = $gameType;
        return $this;
    }

    public function getCreatedByUser(): ?User
    {
        return $this->createdByUser;
    }

    public function setCreatedByUser(?User $createdByUser): static
    {
        $this->createdByUser = $createdByUser;
        return $this;
    }

    public function isOfficial(): bool
    {
        return $this->isOfficial;
    }

    public function setOfficial(bool $isOfficial): static
    {
        $this->isOfficial = $isOfficial;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return Collection<int, GamePosition>
     */
    public function getGamePositions(): Collection
    {
        return $this->gamePositions;
    }

    public function addGamePosition(GamePosition $gamePosition): static
    {
        if (!$this->gamePositions->contains($gamePosition)) {
            $this->gamePositions->add($gamePosition);
            $gamePosition->setGame($this);
        }
        return $this;
    }

    public function removeGamePosition(GamePosition $gamePosition): static
    {
        if ($this->gamePositions->removeElement($gamePosition)) {
            if ($gamePosition->getGame() === $this) {
                $gamePosition->setGame(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, GameSession>
     */
    public function getGameSessions(): Collection
    {
        return $this->gameSessions;
    }

    public function addGameSession(GameSession $gameSession): static
    {
        if (!$this->gameSessions->contains($gameSession)) {
            $this->gameSessions->add($gameSession);
            $gameSession->setGame($this);
        }
        return $this;
    }

    public function removeGameSession(GameSession $gameSession): static
    {
        if ($this->gameSessions->removeElement($gameSession)) {
            if ($gameSession->getGame() === $this) {
                $gameSession->setGame(null);
            }
        }
        return $this;
    }
}
