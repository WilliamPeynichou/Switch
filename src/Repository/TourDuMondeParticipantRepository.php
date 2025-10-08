<?php

namespace App\Repository;

use App\Entity\TourDuMondeParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TourDuMondeParticipant>
 */
class TourDuMondeParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TourDuMondeParticipant::class);
    }

    /**
     * Trouve tous les participants actifs
     */
    public function findActiveParticipants(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le prochain numéro de joueur disponible
     */
    public function getNextPlayerNumber(): int
    {
        $result = $this->createQueryBuilder('p')
            ->select('MAX(p.playerNumber)')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int)$result + 1 : 1;
    }

    /**
     * Vide tous les participants (pour fin de jeu)
     */
    public function clearAllParticipants(): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * Désactive tous les participants
     */
    public function deactivateAllParticipants(): void
    {
        $this->createQueryBuilder('p')
            ->update()
            ->set('p.isActive', ':inactive')
            ->set('p.updatedAt', ':now')
            ->setParameter('inactive', false)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
