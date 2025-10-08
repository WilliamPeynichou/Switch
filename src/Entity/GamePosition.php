<?php

namespace App\Entity;

use App\Repository\GamePositionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePositionRepository::class)]
class GamePosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Game::class, inversedBy: 'gamePositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column]
    private ?int $positionNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $positionName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $positionDescription = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $coordinatesX = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $coordinatesY = null;

    #[ORM\Column]
    private bool $isFinalPosition = false;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $pointsValue = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getPositionNumber(): ?int
    {
        return $this->positionNumber;
    }

    public function setPositionNumber(int $positionNumber): static
    {
        $this->positionNumber = $positionNumber;
        return $this;
    }

    public function getPositionName(): ?string
    {
        return $this->positionName;
    }

    public function setPositionName(string $positionName): static
    {
        $this->positionName = $positionName;
        return $this;
    }

    public function getPositionDescription(): ?string
    {
        return $this->positionDescription;
    }

    public function setPositionDescription(?string $positionDescription): static
    {
        $this->positionDescription = $positionDescription;
        return $this;
    }

    public function getCoordinatesX(): ?float
    {
        return $this->coordinatesX;
    }

    public function setCoordinatesX(?float $coordinatesX): static
    {
        $this->coordinatesX = $coordinatesX;
        return $this;
    }

    public function getCoordinatesY(): ?float
    {
        return $this->coordinatesY;
    }

    public function setCoordinatesY(?float $coordinatesY): static
    {
        $this->coordinatesY = $coordinatesY;
        return $this;
    }

    public function isFinalPosition(): bool
    {
        return $this->isFinalPosition;
    }

    public function setFinalPosition(bool $isFinalPosition): static
    {
        $this->isFinalPosition = $isFinalPosition;
        return $this;
    }

    public function getPointsValue(): ?int
    {
        return $this->pointsValue;
    }

    public function setPointsValue(?int $pointsValue): static
    {
        $this->pointsValue = $pointsValue;
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
