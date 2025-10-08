<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * Trouve les jeux actifs
     */
    public function findActiveGames(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les jeux officiels
     */
    public function findOfficialGames(): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.isOfficial = :official')
            ->andWhere('g.isActive = :active')
            ->setParameter('official', true)
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les jeux par type
     */
    public function findByGameType(string $gameType): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.gameType = :gameType')
            ->andWhere('g.isActive = :active')
            ->setParameter('gameType', $gameType)
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les jeux par nombre de joueurs
     */
    public function findByPlayerCount(int $playerCount): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.minPlayers <= :playerCount')
            ->andWhere('g.maxPlayers >= :playerCount')
            ->andWhere('g.isActive = :active')
            ->setParameter('playerCount', $playerCount)
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les jeux les plus populaires (avec le plus de sessions)
     */
    public function findMostPopularGames(int $limit = 5): array
    {
        return $this->createQueryBuilder('g')
            ->select('g, COUNT(gs.id) as sessionCount')
            ->leftJoin('g.gameSessions', 'gs')
            ->andWhere('g.isActive = :active')
            ->setParameter('active', true)
            ->groupBy('g.id')
            ->orderBy('sessionCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche de jeux par nom
     */
    public function findByNameLike(string $name): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.name LIKE :name')
            ->andWhere('g.isActive = :active')
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('active', true)
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les jeux créés par un utilisateur
     */
    public function findByCreator(int $userId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.createdByUser = :userId')
            ->andWhere('g.isActive = :active')
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->orderBy('g.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
