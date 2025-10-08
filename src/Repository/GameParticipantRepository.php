<?php

namespace App\Repository;

use App\Entity\GameParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameParticipant>
 */
class GameParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameParticipant::class);
    }

    /**
     * Trouve tous les participants actifs
     */
    public function findActiveParticipants(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.isEliminated = :notEliminated')
            ->setParameter('active', true)
            ->setParameter('notEliminated', false)
            ->orderBy('p.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les participants non éliminés
     */
    public function findNonEliminatedParticipants(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isEliminated = :notEliminated')
            ->setParameter('notEliminated', false)
            ->orderBy('p.currentPosition', 'DESC')
            ->addOrderBy('p.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le finaliste
     */
    public function findFinalist(): ?GameParticipant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isFinalist = :finalist')
            ->setParameter('finalist', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les participants éligibles pour la roue (non finalistes, non éliminés)
     */
    public function findWheelEligibleParticipants(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isEliminated = :notEliminated')
            ->andWhere('p.isFinalist = :notFinalist')
            ->andWhere('p.isActive = :active')
            ->setParameter('notEliminated', false)
            ->setParameter('notFinalist', false)
            ->setParameter('active', true)
            ->orderBy('p.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si un finaliste existe et s'il y a des participants éligibles pour la roue
     */
    public function canCreateWheel(): array
    {
        $finalist = $this->findFinalist();
        $eligibleParticipants = $this->findWheelEligibleParticipants();
        
        return [
            'hasFinalist' => $finalist !== null,
            'finalist' => $finalist,
            'eligibleCount' => count($eligibleParticipants),
            'eligibleParticipants' => $eligibleParticipants,
            'canCreateWheel' => $finalist !== null && count($eligibleParticipants) >= 1
        ];
    }

    /**
     * Trouve le prochain numéro de joueur disponible
     */
    public function getNextPlayerNumber(): int
    {
        $result = $this->createQueryBuilder('p')
            ->select('MAX(p.playerNumber)')
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (int)$result + 1 : 1;
    }

    /**
     * Trouve le classement des participants
     */
    public function findRanking(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.currentPosition', 'DESC')
            ->addOrderBy('p.playerNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les statistiques globales
     */
    public function getGlobalStats(): array
    {
        $qb = $this->createQueryBuilder('p');
        
        return $qb->select([
                'COUNT(p.id) as totalParticipants',
                'SUM(p.shotsAttempted) as totalShotsAttempted',
                'SUM(p.shotsMade) as totalShotsMade',
                'AVG(p.shotsMade / NULLIF(p.shotsAttempted, 0)) as averageSuccessRate'
            ])
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Vide tous les participants
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
