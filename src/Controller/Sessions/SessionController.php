<?php

namespace App\Controller\Sessions;

use App\DTO\SessionUpdateDTO;
use App\Entity\GameSession;
use App\Entity\GameEvent;
use App\Entity\GameStatistic;
use App\Repository\GameSessionRepository;
use App\Service\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Contrôleur pour la gestion des sessions de jeu
 * Gère : création, mise à jour, pause, reprise, finalisation
 */
class SessionController extends AbstractController
{
    public function __construct(
        private GameSessionRepository $gameSessionRepository,
        private EntityManagerInterface $entityManager,
        private SessionService $sessionService,
        private ValidatorInterface $validator
    ) {}

    /**
     * Affichage d'une session
     * Route: /session/{id}
     */
    #[Route('/session/{id}', name: 'app_session_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(GameSession $gameSession): Response
    {
        // Vérifier que l'utilisateur a accès à cette session
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette session.');
        }

        return $this->render('frontend/session/show.html.twig', [
            'session' => $gameSession
        ]);
    }

    /**
     * Création d'une nouvelle session
     * Route: /session/create
     */
    #[Route('/session/create', name: 'app_session_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $gameId = $request->request->get('gameId');
            $sessionName = $request->request->get('sessionName');
            $difficultyId = $request->request->get('difficultyId');
            
            $game = $this->entityManager->getRepository(\App\Entity\Game::class)->find($gameId);
            $difficulty = $this->entityManager->getRepository(\App\Entity\Difficulty::class)->find($difficultyId);
            
            if (!$game || !$difficulty) {
                $this->addFlash('error', 'Jeu ou difficulté non trouvé.');
                return $this->redirectToRoute('app_home');
            }

            $gameSession = new GameSession();
            $gameSession->setGame($game);
            $gameSession->setDifficulty($difficulty);
            $gameSession->setMasterUser($this->getUser());
            $gameSession->setSessionName($sessionName);
            $gameSession->setStatus('active');
            $gameSession->setStartedAt(new \DateTimeImmutable());
            $gameSession->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($gameSession);
            $this->entityManager->flush();

            $this->addFlash('success', 'Session créée avec succès !');
            return $this->redirectToRoute('app_session_show', ['id' => $gameSession->getId()]);
        }

        $games = $this->entityManager->getRepository(\App\Entity\Game::class)->findActiveGames();
        $difficulties = $this->entityManager->getRepository(\App\Entity\Difficulty::class)->findAllOrderedByLevel();

        return $this->render('frontend/session/create.html.twig', [
            'games' => $games,
            'difficulties' => $difficulties
        ]);
    }

    /**
     * Mise à jour de la position d'un participant
     * Route: /session/{id}/update-position
     */
    #[Route('/session/{id}/update-position', name: 'app_session_update_position', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updatePosition(GameSession $gameSession, Request $request): JsonResponse
    {
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $dto = new SessionUpdateDTO($data);
            
            // Validate DTO
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse(['error' => implode(', ', $errorMessages)], 400);
            }

            $this->sessionService->updateParticipantPosition($gameSession, $dto);

            return new JsonResponse([
                'success' => true,
                'newPosition' => $dto->position,
                'eventType' => $dto->eventType
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            error_log('Session update error: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    /**
     * Ajout d'une note à la session
     * Route: /session/{id}/add-note
     */
    #[Route('/session/{id}/add-note', name: 'app_session_add_note', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addNote(GameSession $gameSession, Request $request): JsonResponse
    {
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $noteText = $data['noteText'] ?? null;
            $noteType = $data['noteType'] ?? 'general';

            if (empty($noteText)) {
                return new JsonResponse(['error' => 'Le texte de la note est vide'], 400);
            }

            $this->sessionService->addSessionNote($gameSession, $noteText, $noteType);

            return new JsonResponse(['success' => true, 'noteText' => $noteText, 'noteType' => $noteType]);
        } catch (\Exception $e) {
            error_log('Add note error: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    /**
     * Pause de la session
     * Route: /session/{id}/pause
     */
    #[Route('/session/{id}/pause', name: 'app_session_pause', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function pause(GameSession $gameSession): JsonResponse
    {
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $this->sessionService->pauseSession($gameSession);
            return new JsonResponse(['success' => true, 'status' => 'paused']);
        } catch (\Exception $e) {
            error_log('Pause session error: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    /**
     * Reprise de la session
     * Route: /session/{id}/resume
     */
    #[Route('/session/{id}/resume', name: 'app_session_resume', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function resume(GameSession $gameSession): JsonResponse
    {
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $this->sessionService->resumeSession($gameSession);
            return new JsonResponse(['success' => true, 'status' => 'active']);
        } catch (\Exception $e) {
            error_log('Resume session error: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne du serveur'], 500);
        }
    }

    /**
     * Finalisation de la session
     * Route: /session/{id}/complete
     */
    #[Route('/session/{id}/complete', name: 'app_session_complete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function complete(GameSession $gameSession): JsonResponse
    {
        if ($gameSession->getMasterUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        try {
            $this->sessionService->completeSession($gameSession);
            return new JsonResponse(['success' => true, 'status' => 'completed']);
        } catch (\Exception $e) {
            error_log('Complete session error: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne du serveur'], 500);
        }
    }
}
