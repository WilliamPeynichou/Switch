<?php

namespace App\Repository;

use App\Entity\SessionNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SessionNote>
 */
class SessionNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionNote::class);
    }

    /**
     * Trouve les notes d'une session
     */
    public function findBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes par type
     */
    public function findByNoteType(string $noteType): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('noteType', $noteType)
            ->orderBy('sn.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes récentes
     */
    public function findRecentNotes(int $limit = 20): array
    {
        return $this->createQueryBuilder('sn')
            ->orderBy('sn.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes par session et type
     */
    public function findBySessionAndType(int $sessionId, string $noteType): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('noteType', $noteType)
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche dans les notes par texte
     */
    public function searchByText(string $searchText): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.noteText LIKE :searchText')
            ->setParameter('searchText', '%' . $searchText . '%')
            ->orderBy('sn.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes générales d'une session
     */
    public function findGeneralNotesBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('noteType', 'general')
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes de règles d'une session
     */
    public function findRuleNotesBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('noteType', 'rule')
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes de pénalités d'une session
     */
    public function findPenaltyNotesBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('noteType', 'penalty')
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les notes d'accomplissement d'une session
     */
    public function findAchievementNotesBySession(int $sessionId): array
    {
        return $this->createQueryBuilder('sn')
            ->andWhere('sn.session = :sessionId')
            ->andWhere('sn.noteType = :noteType')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('noteType', 'achievement')
            ->orderBy('sn.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
