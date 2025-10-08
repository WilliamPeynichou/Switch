<?php

namespace App\Repository;

use App\Entity\Difficulty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Difficulty>
 */
class DifficultyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Difficulty::class);
    }

    /**
     * Trouve toutes les difficultés triées par niveau
     */
    public function findAllOrderedByLevel(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.level', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une difficulté par niveau
     */
    public function findByLevel(int $level): ?Difficulty
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.level = :level')
            ->setParameter('level', $level)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve les difficultés les plus utilisées
     */
    public function findMostUsedDifficulties(int $limit = 5): array
    {
        return $this->createQueryBuilder('d')
            ->select('d, COUNT(gs.id) as usageCount')
            ->leftJoin('d.gameSessions', 'gs')
            ->groupBy('d.id')
            ->orderBy('usageCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
