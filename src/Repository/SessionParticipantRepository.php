<?php

namespace App\Repository;

use App\Entity\SessionParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SessionParticipant>
 */
class SessionParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionParticipant::class);
    }

    /**
     * Trouve les participants actifs d'une session
     */
    public function findActiveParticipantsBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.session = :sessionId')
            ->andWhere('sp.isActive = :active')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('active', true)
            ->orderBy('sp.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les participants par session
     */
    public function findBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.session = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('sp.joinedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les participants par utilisateur
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('sp.joinedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les participants anonymes
     */
    public function findAnonymousParticipants(): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.isAnonymous = :anonymous')
            ->setParameter('anonymous', true)
            ->orderBy('sp.joinedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les participants par position actuelle
     */
    public function findByCurrentPosition(int $sessionId, int $position): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.session = :sessionId')
            ->andWhere('sp.currentPosition = :position')
            ->andWhere('sp.isActive = :active')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('position', $position)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les participants actifs d'une session
     */
    public function countActiveParticipantsBySession(int $sessionId): int
    {
        return $this->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->andWhere('sp.session = :sessionId')
            ->andWhere('sp.isActive = :active')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les participants les plus actifs
     */
    public function findMostActiveParticipants(int $limit = 10): array
    {
        return $this->createQueryBuilder('sp')
            ->select('sp.user, COUNT(sp.id) as sessionCount')
            ->andWhere('sp.user IS NOT NULL')
            ->groupBy('sp.user')
            ->orderBy('sessionCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les participants par nom
     */
    public function findByPlayerName(string $playerName): array
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.playerName LIKE :playerName')
            ->setParameter('playerName', '%' . $playerName . '%')
            ->orderBy('sp.joinedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
