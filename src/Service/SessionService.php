<?php

namespace App\Service;

use App\DTO\SessionUpdateDTO;
use App\Entity\GameEvent;
use App\Entity\GameSession;
use App\Entity\SessionParticipant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SessionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    public function updateParticipantPosition(GameSession $session, SessionUpdateDTO $dto): void
    {
        // Validation des données
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Données de session invalides');
        }

        // Trouver le participant
        $participant = $this->entityManager->getRepository(SessionParticipant::class)
            ->find($dto->participantId);

        if (!$participant) {
            throw new \InvalidArgumentException('Participant non trouvé');
        }

        // Vérifier que le participant appartient à cette session
        if ($participant->getSession() !== $session) {
            throw new \InvalidArgumentException('Participant n\'appartient pas à cette session');
        }

        $oldPosition = $participant->getCurrentPosition();
        $participant->setCurrentPosition($dto->position);
        $this->entityManager->flush();

        // Créer un événement
        $event = new GameEvent();
        $event->setSession($session);
        $event->setPlayer($participant);
        $event->setEventType($dto->eventType);
        $event->setFromPosition($oldPosition);
        $event->setToPosition($dto->position);
        $event->setTimestampSeconds(time() - $session->getStartedAt()->getTimestamp());
        $event->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function addSessionNote(GameSession $session, string $noteText, string $noteType = 'general'): void
    {
        if (empty(trim($noteText))) {
            throw new \InvalidArgumentException('La note ne peut pas être vide');
        }

        $sessionNote = new \App\Entity\SessionNote();
        $sessionNote->setSession($session);
        $sessionNote->setNoteText(trim($noteText));
        $sessionNote->setNoteType($noteType);
        $sessionNote->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($sessionNote);
        $this->entityManager->flush();
    }

    public function pauseSession(GameSession $session): void
    {
        if ($session->getStatus() !== 'active') {
            throw new \InvalidArgumentException('Seules les sessions actives peuvent être mises en pause');
        }

        $session->setStatus('paused');
        $session->setPausedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    public function resumeSession(GameSession $session): void
    {
        if ($session->getStatus() !== 'paused') {
            throw new \InvalidArgumentException('Seules les sessions en pause peuvent être reprises');
        }

        $session->setStatus('active');
        $session->setPausedAt(null);
        $this->entityManager->flush();
    }
}
