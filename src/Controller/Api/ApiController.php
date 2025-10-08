<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\GameSession;
use App\Repository\GameRepository;
use App\Repository\GameSessionRepository;
use App\Repository\GameStatisticRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur API pour les données JSON
 * Gère : endpoints API, données pour AJAX, intégrations externes
 */
class ApiController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameSessionRepository $gameSessionRepository,
        private GameStatisticRepository $gameStatisticRepository
    ) {}

    /**
     * Liste des jeux (API)
     * Route: /api/games
     */
    #[Route('/api/games', name: 'app_api_games', methods: ['GET'])]
    public function games(): JsonResponse
    {
        $games = $this->gameRepository->findActiveGames();
        
        $gamesData = [];
        foreach ($games as $game) {
            $gamesData[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'description' => $game->getDescription(),
                'minPlayers' => $game->getMinPlayers(),
                'maxPlayers' => $game->getMaxPlayers(),
                'gameType' => $game->getGameType(),
                'isOfficial' => $game->getIsOfficial()
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $gamesData,
            'count' => count($gamesData)
        ]);
    }

    /**
     * Détails d'un jeu (API)
     * Route: /api/games/{id}
     */
    #[Route('/api/games/{id}', name: 'app_api_game_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function game(int $id): JsonResponse
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            return $this->json([
                'success' => false,
                'error' => 'Jeu non trouvé'
            ], 404);
        }

        $gameData = [
            'id' => $game->getId(),
            'name' => $game->getName(),
            'description' => $game->getDescription(),
            'baseRules' => $game->getBaseRules(),
            'minPlayers' => $game->getMinPlayers(),
            'maxPlayers' => $game->getMaxPlayers(),
            'defaultDurationMinutes' => $game->getDefaultDurationMinutes(),
            'gameType' => $game->getGameType(),
            'isOfficial' => $game->getIsOfficial(),
            'imageUrl' => $game->getImageUrl(),
            'createdAt' => $game->getCreatedAt()->format('Y-m-d H:i:s')
        ];

        return $this->json([
            'success' => true,
            'data' => $gameData
        ]);
    }

    /**
     * Sessions d'un utilisateur (API)
     * Route: /api/user/sessions
     */
    #[Route('/api/user/sessions', name: 'app_api_user_sessions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function userSessions(): JsonResponse
    {
        $user = $this->getUser();
        $sessions = $this->gameSessionRepository->findBy(['masterUser' => $user], ['createdAt' => 'DESC']);

        $sessionsData = [];
        foreach ($sessions as $session) {
            $sessionsData[] = [
                'id' => $session->getId(),
                'sessionName' => $session->getSessionName(),
                'gameName' => $session->getGame()->getName(),
                'status' => $session->getStatus(),
                'startedAt' => $session->getStartedAt()?->format('Y-m-d H:i:s'),
                'createdAt' => $session->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $sessionsData,
            'count' => count($sessionsData)
        ]);
    }

    /**
     * Statistiques d'un jeu (API)
     * Route: /api/games/{id}/stats
     */
    #[Route('/api/games/{id}/stats', name: 'app_api_game_stats', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function gameStats(int $id): JsonResponse
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            return $this->json([
                'success' => false,
                'error' => 'Jeu non trouvé'
            ], 404);
        }

        $gameStats = $this->gameStatisticRepository->findByGame($id);
        $averageScore = $this->gameStatisticRepository->getAverageScoreByGame($id);
        $bestScore = $this->gameStatisticRepository->getBestScoreByGame($id);

        return $this->json([
            'success' => true,
            'data' => [
                'gameId' => $id,
                'gameName' => $game->getName(),
                'totalStats' => count($gameStats),
                'averageScore' => $averageScore,
                'bestScore' => $bestScore,
                'statistics' => $gameStats
            ]
        ]);
    }

    /**
     * Classement général (API)
     * Route: /api/leaderboard
     */
    #[Route('/api/leaderboard', name: 'app_api_leaderboard', methods: ['GET'])]
    public function leaderboard(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 10);
        $gameId = $request->query->get('gameId');

        if ($gameId) {
            $topScores = $this->gameStatisticRepository->findTopScores($limit, $gameId);
        } else {
            $topScores = $this->gameStatisticRepository->findTopScores($limit);
        }

        return $this->json([
            'success' => true,
            'data' => $topScores,
            'count' => count($topScores)
        ]);
    }

    /**
     * Recherche de jeux (API)
     * Route: /api/games/search
     */
    #[Route('/api/games/search', name: 'app_api_games_search', methods: ['GET'])]
    public function searchGames(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $gameType = $request->query->get('type');
        $minPlayers = $request->query->get('minPlayers');
        $maxPlayers = $request->query->get('maxPlayers');

        $games = $this->gameRepository->findByNameLike($query);

        // Filtres supplémentaires
        if ($gameType) {
            $games = array_filter($games, fn($game) => $game->getGameType() === $gameType);
        }

        if ($minPlayers) {
            $games = array_filter($games, fn($game) => $game->getMinPlayers() <= (int)$minPlayers);
        }

        if ($maxPlayers) {
            $games = array_filter($games, fn($game) => $game->getMaxPlayers() >= (int)$maxPlayers);
        }

        $gamesData = [];
        foreach ($games as $game) {
            $gamesData[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'description' => $game->getDescription(),
                'gameType' => $game->getGameType(),
                'minPlayers' => $game->getMinPlayers(),
                'maxPlayers' => $game->getMaxPlayers()
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $gamesData,
            'count' => count($gamesData),
            'query' => $query
        ]);
    }
}
