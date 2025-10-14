<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function findValidToken(string $token): ?PasswordResetToken
    {
        return $this->createQueryBuilder('p')
            ->where('p.token = :token')
            ->andWhere('p.isUsed = :isUsed')
            ->andWhere('p.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('isUsed', false)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findValidTokenByEmail(string $email): ?PasswordResetToken
    {
        return $this->createQueryBuilder('p')
            ->where('p.email = :email')
            ->andWhere('p.isUsed = :isUsed')
            ->andWhere('p.expiresAt > :now')
            ->setParameter('email', $email)
            ->setParameter('isUsed', false)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function invalidateUserTokens(string $email): void
    {
        $this->createQueryBuilder('p')
            ->update()
            ->set('p.isUsed', ':isUsed')
            ->where('p.email = :email')
            ->setParameter('isUsed', true)
            ->setParameter('email', $email)
            ->getQuery()
            ->execute();
    }
}
