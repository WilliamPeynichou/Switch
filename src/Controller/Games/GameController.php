<?php

namespace App\Controller\Games;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\GamePositionRepository;
use App\Repository\GameSessionRepository;
use App\Repository\GameStatisticRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur principal pour la gestion des jeux
 * Gère : affichage, détails, règles, statistiques
 */
class GameController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository,
        private GamePositionRepository $gamePositionRepository,
        private GameSessionRepository $gameSessionRepository,
        private GameStatisticRepository $gameStatisticRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Page d'accueil avec liste des jeux
     * Route: /
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Récupérer tous les jeux actifs
        $games = $this->gameRepository->findActiveGames();
        
        // Récupérer toutes les difficultés
        $difficulties = $this->entityManager->getRepository(\App\Entity\Difficulty::class)->findAllOrderedByLevel();
        
        // Récupérer les jeux les plus populaires
        $popularGamesData = $this->gameRepository->findMostPopularGames(3);
        $popularGames = [];
        foreach ($popularGamesData as $item) {
            if (is_array($item) && isset($item[0])) {
                $popularGames[] = $item[0];
            }
        }

        return $this->render('frontend/home/index_new.html.twig', [
            'games' => $games,
            'difficulties' => $difficulties,
            'popularGames' => $popularGames
        ]);
    }

    /**
     * Affichage d'un jeu spécifique
     * Route: /game/{id}
     */
    #[Route('/game/{id}', name: 'app_game_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        // Redirection vers les pages spécifiques selon le nom du jeu
        switch ($game->getName()) {
            case 'Tour du Monde':
                return $this->redirectToRoute('app_tour_du_monde');
            case 'HORSE':
                return $this->render('frontend/game/show.html.twig', [
                    'game' => $game,
                    'message' => 'Page HORSE en cours de développement'
                ]);
            case 'Shoot-out':
                return $this->render('frontend/game/show.html.twig', [
                    'game' => $game,
                    'message' => 'Page Shoot-out en cours de développement'
                ]);
            case 'King of the Court':
                return $this->render('frontend/game/show.html.twig', [
                    'game' => $game,
                    'message' => 'Page King of the Court en cours de développement'
                ]);
            default:
                return $this->render('frontend/game/show.html.twig', [
                    'game' => $game,
                    'message' => 'Page générique du jeu'
                ]);
        }
    }

    /**
     * Interface de jeu
     * Route: /game/{id}/play
     */
    #[Route('/game/{id}/play', name: 'app_game_play', requirements: ['id' => '\d+'])]
    public function play(int $id): Response
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        $positions = $this->gamePositionRepository->findByGame($id);
        $difficulties = $this->entityManager->getRepository(\App\Entity\Difficulty::class)->findAllOrderedByLevel();

        return $this->render('frontend/game/play.html.twig', [
            'game' => $game,
            'positions' => $positions,
            'difficulties' => $difficulties
        ]);
    }

    /**
     * Règles détaillées du jeu
     * Route: /game/{id}/rules
     */
    #[Route('/game/{id}/rules', name: 'app_game_rules', requirements: ['id' => '\d+'])]
    public function rules(int $id): Response
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        $positions = $this->gamePositionRepository->findByGame($id);

        return $this->render('frontend/game/rules.html.twig', [
            'game' => $game,
            'positions' => $positions
        ]);
    }

    /**
     * Statistiques d'un jeu
     * Route: /game/{id}/stats
     */
    #[Route('/game/{id}/stats', name: 'app_game_stats', requirements: ['id' => '\d+'])]
    public function stats(int $id): Response
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        // Récupérer les statistiques du jeu
        $gameStats = $this->gameStatisticRepository->findByGame($id);
        $averageScore = $this->gameStatisticRepository->getAverageScoreByGame($id);
        $bestScore = $this->gameStatisticRepository->getBestScoreByGame($id);

        return $this->render('frontend/game/stats.html.twig', [
            'game' => $game,
            'gameStats' => $gameStats,
            'averageScore' => $averageScore,
            'bestScore' => $bestScore
        ]);
    }
}
