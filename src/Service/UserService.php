<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private ValidatorInterface $validator
    ) {}

    public function registerUser(User $user, string $plainPassword): void
    {
        // Validation des données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Données utilisateur invalides');
        }

        // Encoder le mot de passe
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $plainPassword)
        );

        // Définir les propriétés par défaut
        $user->setRole('ROLE_USER');
        $user->setActive(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateUserProfile(User $user, ?string $plainPassword = null): void
    {
        // Validation des données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Données utilisateur invalides');
        }

        // Mettre à jour le mot de passe si fourni
        if ($plainPassword) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $plainPassword)
            );
        }

        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
