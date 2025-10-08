<?php

namespace App\Repository;

use App\Entity\GameHistory;
use App\Entity\User;
use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameHistory>
 */
class GameHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameHistory::class);
    }

    /**
     * Trouve l'historique d'un utilisateur
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :user')
            ->setParameter('user', $user)
            ->orderBy('h.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve l'historique d'un jeu spécifique
     */
    public function findByGame(Game $game): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.game = :game')
            ->setParameter('game', $game)
            ->orderBy('h.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve l'historique d'un utilisateur pour un jeu spécifique
     */
    public function findByUserAndGame(User $user, Game $game): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :user')
            ->andWhere('h.game = :game')
            ->setParameter('user', $user)
            ->setParameter('game', $game)
            ->orderBy('h.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les statistiques d'un utilisateur
     */
    public function getUserStats(User $user): array
    {
        $qb = $this->createQueryBuilder('h');
        
        return $qb->select([
                'COUNT(h.id) as totalGames',
                'AVG(h.totalParticipants) as avgParticipants',
                'AVG(h.totalDurationSeconds) as avgDuration',
                'SUM(CASE WHEN h.winnerId IS NOT NULL THEN 1 ELSE 0 END) as wins'
            ])
            ->andWhere('h.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Trouve les parties récentes
     */
    public function findRecentGames(int $limit = 10): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.completedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les parties d'aujourd'hui
     */
    public function findTodayGames(): array
    {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');
        
        return $this->createQueryBuilder('h')
            ->andWhere('h.completedAt >= :today')
            ->andWhere('h.completedAt < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->orderBy('h.completedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
