<?php

namespace App\Repository;

use App\Entity\GamePosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GamePosition>
 */
class GamePositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GamePosition::class);
    }

    /**
     * Trouve les positions d'un jeu
     */
    public function findByGame(int $gameId): array
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('gp.positionNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une position par numéro dans un jeu
     */
    public function findByGameAndPositionNumber(int $gameId, int $positionNumber): ?GamePosition
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.game = :gameId')
            ->andWhere('gp.positionNumber = :positionNumber')
            ->setParameter('gameId', $gameId)
            ->setParameter('positionNumber', $positionNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les positions finales d'un jeu
     */
    public function findFinalPositionsByGame(int $gameId): array
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.game = :gameId')
            ->andWhere('gp.isFinalPosition = :final')
            ->setParameter('gameId', $gameId)
            ->setParameter('final', true)
            ->orderBy('gp.positionNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les positions par valeur de points
     */
    public function findByPointsValue(int $pointsValue): array
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.pointsValue = :pointsValue')
            ->setParameter('pointsValue', $pointsValue)
            ->orderBy('gp.positionNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve la première position d'un jeu
     */
    public function findFirstPositionByGame(int $gameId): ?GamePosition
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('gp.positionNumber', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve la dernière position d'un jeu
     */
    public function findLastPositionByGame(int $gameId): ?GamePosition
    {
        return $this->createQueryBuilder('gp')
            ->andWhere('gp.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('gp.positionNumber', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
