<?php

namespace App\Controller\Statistics;

use App\Repository\GameStatisticRepository;
use App\Repository\GameSessionRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour la gestion des statistiques
 * Gère : statistiques globales, par jeu, par utilisateur, classements
 */
class StatisticsController extends AbstractController
{
    public function __construct(
        private GameStatisticRepository $gameStatisticRepository,
        private GameSessionRepository $gameSessionRepository,
        private GameRepository $gameRepository
    ) {}

    /**
     * Tableau de bord des statistiques
     * Route: /statistics/
     */
    #[Route('/statistics/', name: 'app_statistics_index')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        // Statistiques globales
        $totalSessions = $this->gameSessionRepository->count(['masterUser' => $user]);
        $totalGames = $this->gameRepository->count(['isActive' => true]);
        
        // Statistiques personnelles
        $userStats = $this->gameStatisticRepository->findByUser($user->getId());
        $totalPoints = array_sum(array_column($userStats, 'totalPoints'));
        $averageScore = count($userStats) > 0 ? $totalPoints / count($userStats) : 0;
        
        // Jeux les plus joués
        $mostPlayedGames = $this->gameStatisticRepository->findMostActivePlayers(5);
        
        // Classement général
        $leaderboard = $this->gameStatisticRepository->findTopScores(10);

        return $this->render('frontend/statistics/index.html.twig', [
            'totalSessions' => $totalSessions,
            'totalGames' => $totalGames,
            'userStats' => $userStats,
            'totalPoints' => $totalPoints,
            'averageScore' => $averageScore,
            'mostPlayedGames' => $mostPlayedGames,
            'leaderboard' => $leaderboard
        ]);
    }

    /**
     * Sessions de l'utilisateur
     * Route: /statistics/my-sessions
     */
    #[Route('/statistics/my-sessions', name: 'app_statistics_my_sessions')]
    #[IsGranted('ROLE_USER')]
    public function mySessions(): Response
    {
        $user = $this->getUser();
        $sessions = $this->gameSessionRepository->findBy(['masterUser' => $user], ['createdAt' => 'DESC']);

        return $this->render('frontend/statistics/my_sessions.html.twig', [
            'sessions' => $sessions
        ]);
    }

    /**
     * Statistiques par jeu
     * Route: /statistics/game/{id}
     */
    #[Route('/statistics/game/{id}', name: 'app_statistics_by_game', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function byGame(int $id): Response
    {
        $game = $this->gameRepository->find($id);
        
        if (!$game) {
            throw $this->createNotFoundException('Jeu non trouvé');
        }

        $gameStats = $this->gameStatisticRepository->findByGame($id);
        $averageScore = $this->gameStatisticRepository->getAverageScoreByGame($id);
        $bestScore = $this->gameStatisticRepository->getBestScoreByGame($id);
        $averageShots = $this->gameStatisticRepository->getAverageShotsByGame($id);
        $successRate = $this->gameStatisticRepository->getSuccessRateByGame($id);

        return $this->render('frontend/statistics/by_game.html.twig', [
            'game' => $game,
            'gameStats' => $gameStats,
            'averageScore' => $averageScore,
            'bestScore' => $bestScore,
            'averageShots' => $averageShots,
            'successRate' => $successRate
        ]);
    }

    /**
     * Classement général
     * Route: /statistics/leaderboard
     */
    #[Route('/statistics/leaderboard', name: 'app_statistics_leaderboard')]
    public function leaderboard(): Response
    {
        $topScores = $this->gameStatisticRepository->findTopScores(50);
        $mostActivePlayers = $this->gameStatisticRepository->findMostActivePlayers(20);
        $bestShooters = $this->gameStatisticRepository->findBestShooters(20);

        return $this->render('frontend/statistics/leaderboard.html.twig', [
            'topScores' => $topScores,
            'mostActivePlayers' => $mostActivePlayers,
            'bestShooters' => $bestShooters
        ]);
    }

    /**
     * Statistiques détaillées d'un utilisateur
     * Route: /statistics/user/{id}
     */
    #[Route('/statistics/user/{id}', name: 'app_statistics_user', requirements: ['id' => '\d+'])]
    public function userStats(int $id): Response
    {
        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $userStats = $this->gameStatisticRepository->findByUser($id);
        $averageStats = $this->gameStatisticRepository->getPlayerAverageStats($id);
        $successRate = $this->gameStatisticRepository->getPlayerSuccessRate($id);

        return $this->render('frontend/statistics/user_stats.html.twig', [
            'user' => $user,
            'userStats' => $userStats,
            'averageStats' => $averageStats,
            'successRate' => $successRate
        ]);
    }
}
