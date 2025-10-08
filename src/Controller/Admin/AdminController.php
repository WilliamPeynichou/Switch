<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Difficulty;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Repository\GameSessionRepository;
use App\Repository\GameStatisticRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration
 * Gère : tableau de bord, gestion des jeux, utilisateurs, statistiques globales
 */
class AdminController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository,
        private UserRepository $userRepository,
        private GameSessionRepository $gameSessionRepository,
        private GameStatisticRepository $gameStatisticRepository
    ) {}

    /**
     * Tableau de bord administrateur
     * Route: /admin
     */
    #[Route('/admin', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(): Response
    {
        // Statistiques globales
        $totalUsers = $this->userRepository->countActiveUsers();
        $totalGames = $this->gameRepository->count(['isActive' => true]);
        $totalSessions = $this->gameSessionRepository->count([]);
        $totalStatistics = $this->gameStatisticRepository->count([]);

        // Utilisateurs récents
        $recentUsers = $this->userRepository->findBy([], ['createdAt' => 'DESC'], 5);
        
        // Sessions récentes
        $recentSessions = $this->gameSessionRepository->findBy([], ['createdAt' => 'DESC'], 5);
        
        // Jeux les plus populaires
        $popularGames = $this->gameRepository->findMostPopularGames(5);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalGames' => $totalGames,
            'totalSessions' => $totalSessions,
            'totalStatistics' => $totalStatistics,
            'recentUsers' => $recentUsers,
            'recentSessions' => $recentSessions,
            'popularGames' => $popularGames
        ]);
    }

    /**
     * Gestion des jeux
     * Route: /admin/games
     */
    #[Route('/admin/games', name: 'app_admin_games')]
    #[IsGranted('ROLE_ADMIN')]
    public function games(): Response
    {
        $games = $this->gameRepository->findAll();
        
        return $this->render('admin/games/index.html.twig', [
            'games' => $games
        ]);
    }

    /**
     * Création d'un nouveau jeu
     * Route: /admin/games/create
     */
    #[Route('/admin/games/create', name: 'app_admin_games_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createGame(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $game = new Game();
            $game->setName($request->request->get('name'));
            $game->setDescription($request->request->get('description'));
            $game->setBaseRules($request->request->get('baseRules'));
            $game->setMinPlayers((int)$request->request->get('minPlayers'));
            $game->setMaxPlayers((int)$request->request->get('maxPlayers'));
            $game->setDefaultDurationMinutes((int)$request->request->get('defaultDurationMinutes'));
            $game->setGameType($request->request->get('gameType'));
            $game->setIsOfficial(true);
            $game->setCreatedByUser($this->getUser());
            $game->setIsActive(true);
            $game->setCreatedAt(new \DateTimeImmutable());
            $game->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($game);
            $this->entityManager->flush();

            $this->addFlash('success', 'Jeu créé avec succès !');
            return $this->redirectToRoute('app_admin_games');
        }

        return $this->render('admin/games/create.html.twig');
    }

    /**
     * Gestion des utilisateurs
     * Route: /admin/users
     */
    #[Route('/admin/users', name: 'app_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(): Response
    {
        $users = $this->userRepository->findAll();
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Détails d'un utilisateur
     * Route: /admin/users/{id}
     */
    #[Route('/admin/users/{id}', name: 'app_admin_users_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $userSessions = $this->gameSessionRepository->findBy(['masterUser' => $user]);
        $userStats = $this->gameStatisticRepository->findByUser($id);

        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
            'userSessions' => $userSessions,
            'userStats' => $userStats
        ]);
    }

    /**
     * Statistiques globales
     * Route: /admin/statistics
     */
    #[Route('/admin/statistics', name: 'app_admin_statistics')]
    #[IsGranted('ROLE_ADMIN')]
    public function statistics(): Response
    {
        $totalUsers = $this->userRepository->countActiveUsers();
        $totalGames = $this->gameRepository->count(['isActive' => true]);
        $totalSessions = $this->gameSessionRepository->count([]);
        
        $averageScore = $this->gameStatisticRepository->getAverageScoreByGame(null);
        $bestScore = $this->gameStatisticRepository->findTopScores(1);
        $mostActivePlayers = $this->gameStatisticRepository->findMostActivePlayers(10);

        return $this->render('admin/statistics.html.twig', [
            'totalUsers' => $totalUsers,
            'totalGames' => $totalGames,
            'totalSessions' => $totalSessions,
            'averageScore' => $averageScore,
            'bestScore' => $bestScore,
            'mostActivePlayers' => $mostActivePlayers
        ]);
    }
}
