<?php

namespace App\Repository;

use App\Entity\GameEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameEvent>
 */
class GameEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameEvent::class);
    }

    /**
     * Trouve les événements par session
     */
    public function findBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.session = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('ge.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements par joueur
     */
    public function findByPlayer(int $playerId): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.player = :playerId')
            ->setParameter('playerId', $playerId)
            ->orderBy('ge.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements par type
     */
    public function findByEventType(string $eventType): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.eventType = :eventType')
            ->setParameter('eventType', $eventType)
            ->orderBy('ge.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements récents
     */
    public function findRecentEvents(int $limit = 20): array
    {
        return $this->createQueryBuilder('ge')
            ->orderBy('ge.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements par session et type
     */
    public function findBySessionAndType(int $sessionId, string $eventType): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.session = :sessionId')
            ->andWhere('ge.eventType = :eventType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('eventType', $eventType)
            ->orderBy('ge.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les événements par type dans une session
     */
    public function countByTypeInSession(int $sessionId, string $eventType): int
    {
        return $this->createQueryBuilder('ge')
            ->select('COUNT(ge.id)')
            ->andWhere('ge.session = :sessionId')
            ->andWhere('ge.eventType = :eventType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('eventType', $eventType)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les événements de score dans une session
     */
    public function findScoreEventsInSession(int $sessionId): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.session = :sessionId')
            ->andWhere('ge.eventType = :eventType')
            ->andWhere('ge.pointsScored > 0')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('eventType', 'shot_made')
            ->orderBy('ge.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements d'airball dans une session
     */
    public function findAirballEventsInSession(int $sessionId): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.session = :sessionId')
            ->andWhere('ge.eventType = :eventType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('eventType', 'airball')
            ->orderBy('ge.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les événements de brique dans une session
     */
    public function findBrickEventsInSession(int $sessionId): array
    {
        return $this->createQueryBuilder('ge')
            ->andWhere('ge.session = :sessionId')
            ->andWhere('ge.eventType = :eventType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('eventType', 'brick')
            ->orderBy('ge.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
