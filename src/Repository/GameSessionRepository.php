<?php

namespace App\Repository;

use App\Entity\GameSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSession>
 */
class GameSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSession::class);
    }

    /**
     * Trouve les sessions actives
     */
    public function findActiveSessions(): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.status = :status')
            ->setParameter('status', 'active')
            ->orderBy('gs.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions par maître de jeu
     */
    public function findByMaster(int $masterId): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.masterUser = :masterId')
            ->setParameter('masterId', $masterId)
            ->orderBy('gs.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions par jeu
     */
    public function findByGame(int $gameId): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('gs.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.status = :status')
            ->setParameter('status', $status)
            ->orderBy('gs.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions récentes
     */
    public function findRecentSessions(int $limit = 10): array
    {
        return $this->createQueryBuilder('gs')
            ->orderBy('gs.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions en cours
     */
    public function findOngoingSessions(): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.status IN (:statuses)')
            ->setParameter('statuses', ['active', 'paused'])
            ->orderBy('gs.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions terminées
     */
    public function findCompletedSessions(): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.status = :status')
            ->setParameter('status', 'completed')
            ->orderBy('gs.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les sessions par difficulté
     */
    public function findByDifficulty(int $difficultyId): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.difficulty = :difficultyId')
            ->setParameter('difficultyId', $difficultyId)
            ->orderBy('gs.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les sessions par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('gs')
            ->select('COUNT(gs.id)')
            ->andWhere('gs.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les sessions avec le plus de participants
     */
    public function findSessionsWithMostParticipants(int $limit = 5): array
    {
        return $this->createQueryBuilder('gs')
            ->select('gs, COUNT(sp.id) as participantCount')
            ->leftJoin('gs.sessionParticipants', 'sp')
            ->groupBy('gs.id')
            ->orderBy('participantCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
