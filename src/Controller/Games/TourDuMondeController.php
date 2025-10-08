<?php

namespace App\Controller\Games;

use App\Entity\Game;
use App\Entity\Difficulty;
use App\Entity\GameSession;
use App\Entity\SessionParticipant;
use App\Repository\GameRepository;
use App\Repository\DifficultyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur spécialisé pour le jeu "Tour du Monde"
 * Gère : règles, configuration, interface de jeu, roue de sélection
 */
class TourDuMondeController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository,
        private DifficultyRepository $difficultyRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Page principale du jeu Tour du Monde
     * Route: /tour-du-monde
     */
    #[Route('/tour-du-monde', name: 'app_tour_du_monde')]
    public function index(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        if (!$game) {
            throw $this->createNotFoundException('Le jeu "Tour du Monde" n\'existe pas.');
        }

        $difficulties = $this->difficultyRepository->findAllOrderedByLevel();

        return $this->render('frontend/games/tour_du_monde/index.html.twig', [
            'game' => $game,
            'difficulties' => $difficulties
        ]);
    }

    /**
     * Configuration et démarrage du jeu
     * Route: /tour-du-monde/setup
     */
    #[Route('/tour-du-monde/setup', name: 'app_tour_du_monde_setup', methods: ['POST'])]
    public function setup(Request $request): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        if (!$game) {
            $this->addFlash('error', 'Jeu "Tour du Monde" non trouvé.');
            return $this->redirectToRoute('app_home');
        }

        $sessionName = $request->request->get('sessionName');
        $difficultyId = $request->request->get('difficulty');
        $participants = $request->request->all('participants');
        
        $difficulty = $this->difficultyRepository->find($difficultyId);
        if (!$difficulty) {
            $this->addFlash('error', 'Difficulté non valide.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Validation des données
        $participants = array_filter($participants, function($participant) {
            return !empty(trim($participant));
        });

        if (empty($sessionName) || empty($difficultyId) || empty($participants)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        if (count($participants) < 2) {
            $this->addFlash('error', 'Il faut au moins 2 participants pour jouer.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Création de la session
        $gameSession = new GameSession();
        $gameSession->setGame($game);
        $gameSession->setDifficulty($difficulty);
        $gameSession->setMasterUser($this->getUser());
        $gameSession->setSessionName($sessionName);
        $gameSession->setStatus('active');
        $gameSession->setStartedAt(new \DateTimeImmutable());
        $gameSession->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($gameSession);

        // Création des participants
        foreach ($participants as $index => $participantName) {
            $sessionParticipant = new SessionParticipant();
            $sessionParticipant->setSession($gameSession);
            $sessionParticipant->setPlayerName($participantName);
            $sessionParticipant->setIsAnonymous(true);
            $sessionParticipant->setPlayerNumber($index + 1);
            $sessionParticipant->setCurrentPosition(1);
            $sessionParticipant->setIsActive(true);
            $sessionParticipant->setJoinedAt(new \DateTimeImmutable());

            $this->entityManager->persist($sessionParticipant);
        }

        $this->entityManager->flush();

        return $this->render('frontend/games/tour_du_monde/game.html.twig', [
            'game' => $game,
            'difficulty' => $difficulty,
            'sessionName' => $sessionName,
            'participants' => $participants,
            'gameSession' => $gameSession
        ]);
    }

    /**
     * Roue de sélection pour le 1v1 final
     * Route: /tour-du-monde/wheel
     */
    #[Route('/tour-du-monde/wheel', name: 'app_tour_du_monde_wheel', methods: ['POST'])]
    public function wheel(Request $request): Response
    {
        $participants = $request->request->all('participants');
        
        if (empty($participants)) {
            return $this->json(['error' => 'Aucun participant'], 400);
        }
        
        $randomIndex = array_rand($participants);
        $selectedParticipant = $participants[$randomIndex];

        return $this->json(['winner' => $selectedParticipant]);
    }

    /**
     * Règles détaillées du jeu
     * Route: /tour-du-monde/rules
     */
    #[Route('/tour-du-monde/rules', name: 'app_tour_du_monde_rules')]
    public function rules(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        if (!$game) {
            throw $this->createNotFoundException('Le jeu "Tour du Monde" n\'existe pas.');
        }

        return $this->render('frontend/games/tour_du_monde/rules.html.twig', [
            'game' => $game
        ]);
    }

    /**
     * Statistiques du jeu Tour du Monde
     * Route: /tour-du-monde/stats
     */
    #[Route('/tour-du-monde/stats', name: 'app_tour_du_monde_stats')]
    public function stats(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        if (!$game) {
            throw $this->createNotFoundException('Le jeu "Tour du Monde" n\'existe pas.');
        }

        // Récupérer les statistiques spécifiques au Tour du Monde
        $gameStats = $this->entityManager->getRepository(\App\Entity\GameStatistic::class)->findByGame($game->getId());
        $averageScore = $this->entityManager->getRepository(\App\Entity\GameStatistic::class)->getAverageScoreByGame($game->getId());
        $bestScore = $this->entityManager->getRepository(\App\Entity\GameStatistic::class)->getBestScoreByGame($game->getId());

        return $this->render('frontend/games/tour_du_monde/stats.html.twig', [
            'game' => $game,
            'gameStats' => $gameStats,
            'averageScore' => $averageScore,
            'bestScore' => $bestScore
        ]);
    }
}
