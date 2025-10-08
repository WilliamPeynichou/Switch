<?php

namespace App\Repository;

use App\Entity\GameStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameStatistic>
 */
class GameStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameStatistic::class);
    }

    /**
     * Trouve les statistiques par session
     */
    public function findBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.session = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('gs.totalPoints', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les statistiques par joueur
     */
    public function findByPlayer(int $playerId): array
    {
        return $this->createQueryBuilder('gs')
            ->andWhere('gs.player = :playerId')
            ->setParameter('playerId', $playerId)
            ->orderBy('gs.session', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les meilleurs scores
     */
    public function findTopScores(int $limit = 10): array
    {
        return $this->createQueryBuilder('gs')
            ->orderBy('gs.totalPoints', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les statistiques par jeu
     */
    public function findByGame(int $gameId): array
    {
        return $this->createQueryBuilder('gs')
            ->join('gs.session', 's')
            ->andWhere('s.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('gs.totalPoints', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule les statistiques moyennes d'un joueur
     */
    public function getPlayerAverageStats(int $playerId): array
    {
        return $this->createQueryBuilder('gs')
            ->select('AVG(gs.shotsAttempted) as avgShotsAttempted')
            ->addSelect('AVG(gs.shotsMade) as avgShotsMade')
            ->addSelect('AVG(gs.totalPoints) as avgTotalPoints')
            ->addSelect('AVG(gs.timePlayedSeconds) as avgTimePlayed')
            ->andWhere('gs.player = :playerId')
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Trouve les joueurs les plus actifs
     */
    public function findMostActivePlayers(int $limit = 10): array
    {
        return $this->createQueryBuilder('gs')
            ->select('gs.player, COUNT(gs.id) as sessionCount')
            ->addSelect('SUM(gs.totalPoints) as totalPoints')
            ->addSelect('AVG(gs.totalPoints) as avgPoints')
            ->groupBy('gs.player')
            ->orderBy('sessionCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les statistiques par difficulté
     */
    public function findByDifficulty(int $difficultyId): array
    {
        return $this->createQueryBuilder('gs')
            ->join('gs.session', 's')
            ->andWhere('s.difficulty = :difficultyId')
            ->setParameter('difficultyId', $difficultyId)
            ->orderBy('gs.totalPoints', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le pourcentage de réussite d'un joueur
     */
    public function getPlayerSuccessRate(int $playerId): float
    {
        $result = $this->createQueryBuilder('gs')
            ->select('AVG(CASE WHEN gs.shotsAttempted > 0 THEN (gs.shotsMade / gs.shotsAttempted) * 100 ELSE 0 END) as successRate')
            ->andWhere('gs.player = :playerId')
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    /**
     * Trouve les joueurs avec le meilleur pourcentage de réussite
     */
    public function findBestShooters(int $limit = 10): array
    {
        return $this->createQueryBuilder('gs')
            ->select('gs.player')
            ->addSelect('AVG(CASE WHEN gs.shotsAttempted > 0 THEN (gs.shotsMade / gs.shotsAttempted) * 100 ELSE 0 END) as successRate')
            ->addSelect('SUM(gs.shotsAttempted) as totalShots')
            ->addSelect('SUM(gs.shotsMade) as totalMade')
            ->groupBy('gs.player')
            ->having('totalShots >= 10') // Au moins 10 tirs
            ->orderBy('successRate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
