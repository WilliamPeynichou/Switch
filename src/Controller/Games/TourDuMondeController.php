<?php

namespace App\Controller\Games;

use App\Entity\Game;
use App\Entity\GameSession;
use App\Entity\TourDuMondeParticipant;
use App\Entity\GameParticipant;
use App\Entity\GameHistory;
use App\Repository\GameRepository;
use App\Repository\DifficultyRepository;
use App\Repository\TourDuMondeParticipantRepository;
use App\Repository\GameParticipantRepository;
use App\Repository\GameHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour le jeu Tour du Monde
 */
class TourDuMondeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameRepository $gameRepository,
        private DifficultyRepository $difficultyRepository,
        private TourDuMondeParticipantRepository $participantRepository,
        private GameParticipantRepository $gameParticipantRepository,
        private GameHistoryRepository $gameHistoryRepository
    ) {}

    /**
     * Page principale du Tour du Monde
     * Route: /tour-du-monde
     */
    #[Route('/tour-du-monde', name: 'app_tour_du_monde')]
    public function index(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        $difficulties = $this->difficultyRepository->findAllOrderedByLevel();
        
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('info', 'Vous devez être connecté pour jouer au Tour du Monde. <a href="' . $this->generateUrl('app_login') . '">Se connecter</a> ou <a href="' . $this->generateUrl('app_register') . '">créer un compte</a>.');
        }

        // Récupérer la session active et les participants
        $session = null;
        $participants = [];
        
        if ($user) {
            // Chercher une session active pour cet utilisateur
            $session = $this->entityManager->getRepository(GameSession::class)
                ->findOneBy([
                    'game' => $game,
                    'masterUser' => $user,
                    'status' => 'active'
                ]);
            
            if ($session) {
                $participants = $this->participantRepository->findActiveParticipants();
            }
        }

        return $this->render('frontend/games/tour_du_monde/index.html.twig', [
            'game' => $game,
            'difficulties' => $difficulties,
            'user' => $user,
            'session' => $session,
            'participants' => $participants,
        ]);
    }

    /**
     * Configuration de la session
     * Route: /tour-du-monde/setup
     */
    #[Route('/tour-du-monde/setup', name: 'app_tour_du_monde_setup', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function setup(Request $request): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        $user = $this->getUser();
        
        $sessionName = $request->request->get('sessionName');
        $difficultyId = $request->request->get('difficulty');
        
        if (!$sessionName) {
            $this->addFlash('error', 'Le nom de la session est requis.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Vérifier s'il existe déjà une session active
        $existingSession = $this->entityManager->getRepository(GameSession::class)
            ->findOneBy([
                'game' => $game,
                'masterUser' => $user,
                'status' => 'active'
            ]);

        if ($existingSession) {
            $this->addFlash('info', 'Une session est déjà active. Vous pouvez ajouter des participants.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Créer une nouvelle session
        $session = new GameSession();
        $session->setGame($game);
        $session->setMasterUser($user);
        $session->setSessionName($sessionName);
        $session->setStatus('active');
        $session->setStartedAt(new \DateTimeImmutable());

        if ($difficultyId) {
            $difficulty = $this->difficultyRepository->find($difficultyId);
            if ($difficulty) {
                $session->setDifficulty($difficulty);
            }
        }

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        $this->addFlash('success', 'Session créée avec succès ! Vous pouvez maintenant ajouter des participants.');
        return $this->redirectToRoute('app_tour_du_monde');
    }

    /**
     * Ajouter un participant
     * Route: /tour-du-monde/add-participant
     */
    #[Route('/tour-du-monde/add-participant', name: 'app_tour_du_monde_add_participant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addParticipant(Request $request): Response
    {
        $playerName = trim($request->request->get('playerName'));
        $user = $this->getUser();
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        
        if (!$playerName) {
            $this->addFlash('error', 'Le nom du joueur est requis.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Vérifier si le joueur existe déjà
        $existingParticipant = $this->participantRepository->findOneBy([
            'playerName' => $playerName,
            'isActive' => true
        ]);

        if ($existingParticipant) {
            $this->addFlash('error', 'Ce joueur est déjà dans la liste des participants.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Vérifier s'il y a une session active, sinon en créer une
        $session = $this->entityManager->getRepository(GameSession::class)
            ->findOneBy([
                'game' => $game,
                'masterUser' => $user,
                'status' => 'active'
            ]);

        if (!$session) {
            // Créer une nouvelle session automatiquement
            $session = new GameSession();
            $session->setGame($game);
            $session->setMasterUser($user);
            $session->setSessionName('Tour du Monde - ' . (new \DateTimeImmutable())->format('d/m/Y H:i'));
            $session->setStatus('active');
            $session->setStartedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($session);
            $this->entityManager->flush();
        }

        // Créer un nouveau participant
        $participant = new TourDuMondeParticipant();
        $participant->setPlayerName($playerName);
        $participant->setPlayerNumber($this->participantRepository->getNextPlayerNumber());

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$playerName}' ajouté avec succès !");
        return $this->redirectToRoute('app_tour_du_monde');
    }

    /**
     * Supprimer un participant
     * Route: /tour-du-monde/remove-participant/{id}
     */
    #[Route('/tour-du-monde/remove-participant/{id}', name: 'app_tour_du_monde_remove_participant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function removeParticipant(int $id): Response
    {
        $participant = $this->participantRepository->find($id);
        
        if (!$participant) {
            $this->addFlash('error', 'Participant non trouvé.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        $playerName = $participant->getPlayerName();
        $this->entityManager->remove($participant);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$playerName}' supprimé avec succès !");
        return $this->redirectToRoute('app_tour_du_monde');
    }

    /**
     * Page de jeu Tour du Monde
     * Route: /tour-du-monde/game
     */
    #[Route('/tour-du-monde/game', name: 'app_tour_du_monde_game')]
    #[IsGranted('ROLE_USER')]
    public function game(): Response
    {
        $setupParticipants = $this->participantRepository->findActiveParticipants();
        
        if (count($setupParticipants) < 2) {
            $this->addFlash('error', 'Il faut au moins 2 participants pour commencer le jeu.');
            return $this->redirectToRoute('app_tour_du_monde');
        }

        // Vérifier si les participants de jeu existent déjà
        $gameParticipants = $this->gameParticipantRepository->findActiveParticipants();
        
        // Si pas de participants de jeu, les créer à partir des participants de setup
        if (empty($gameParticipants)) {
            $this->initializeGameParticipants($setupParticipants);
            $gameParticipants = $this->gameParticipantRepository->findActiveParticipants();
        }

        // Optimisation : Récupérer les données de la roue en une seule requête
        $wheelData = $this->gameParticipantRepository->canCreateWheel();

        return $this->render('frontend/games/tour_du_monde/gameTDM.html.twig', [
            'participants' => $gameParticipants,
            'setupParticipants' => $setupParticipants,
            'wheelData' => $wheelData,
        ]);
    }

    /**
     * Initialiser les participants de jeu
     */
    private function initializeGameParticipants(array $setupParticipants): void
    {
        foreach ($setupParticipants as $setupParticipant) {
            $gameParticipant = new GameParticipant();
            $gameParticipant->setPlayerName($setupParticipant->getPlayerName());
            $gameParticipant->setPlayerNumber($setupParticipant->getPlayerNumber());
            $gameParticipant->setCurrentPosition(0);
            $gameParticipant->setScore(0);
            
            $this->entityManager->persist($gameParticipant);
        }
        
        $this->entityManager->flush();
    }

    /**
     * Ajouter un participant pendant le jeu
     * Route: /tour-du-monde/game/add-participant
     */
    #[Route('/tour-du-monde/game/add-participant', name: 'app_tour_du_monde_game_add_participant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addGameParticipant(Request $request): Response
    {
        $playerName = trim($request->request->get('playerName'));
        
        if (!$playerName) {
            $this->addFlash('error', 'Le nom du joueur est requis.');
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        // Vérifier si le joueur existe déjà
        $existingParticipant = $this->gameParticipantRepository->findOneBy([
            'playerName' => $playerName,
            'isActive' => true
        ]);

        if ($existingParticipant) {
            $this->addFlash('error', 'Ce joueur est déjà dans le jeu.');
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        // Créer un nouveau participant de jeu
        $participant = new GameParticipant();
        $participant->setPlayerName($playerName);
        $participant->setPlayerNumber($this->gameParticipantRepository->getNextPlayerNumber());
        $participant->setCurrentPosition(0);
        $participant->setScore(0);

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$playerName}' ajouté au jeu !");
        return $this->redirectToRoute('app_tour_du_monde_game');
    }

    /**
     * Supprimer un participant pendant le jeu
     * Route: /tour-du-monde/game/remove-participant/{id}
     */
    #[Route('/tour-du-monde/game/remove-participant/{id}', name: 'app_tour_du_monde_game_remove_participant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function removeGameParticipant(int $id): Response
    {
        $participant = $this->gameParticipantRepository->find($id);
        
        if (!$participant) {
            $this->addFlash('error', 'Participant non trouvé.');
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        $playerName = $participant->getPlayerName();
        $this->entityManager->remove($participant);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$playerName}' supprimé du jeu !");
        return $this->redirectToRoute('app_tour_du_monde_game');
    }

    /**
     * Définir un finaliste
     * Route: /tour-du-monde/game/set-finalist/{id}
     */
    #[Route('/tour-du-monde/game/set-finalist/{id}', name: 'app_tour_du_monde_game_set_finalist', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function setFinalist(int $id): Response
    {
        $participant = $this->gameParticipantRepository->find($id);
        
        if (!$participant) {
            $this->addFlash('error', 'Participant non trouvé.');
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        // Désactiver tous les autres finalistes
        $this->gameParticipantRepository->createQueryBuilder('p')
            ->update()
            ->set('p.isFinalist', ':notFinalist')
            ->setParameter('notFinalist', false)
            ->getQuery()
            ->execute();

        // Définir ce participant comme finaliste
        $participant->setFinalist(true);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$participant->getPlayerName()}' défini comme finaliste !");
        return $this->redirectToRoute('app_tour_du_monde_game');
    }

    /**
     * Éliminer un participant
     * Route: /tour-du-monde/game/eliminate/{id}
     */
    #[Route('/tour-du-monde/game/eliminate/{id}', name: 'app_tour_du_monde_game_eliminate', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function eliminateParticipant(int $id): Response
    {
        $participant = $this->gameParticipantRepository->find($id);
        
        if (!$participant) {
            $this->addFlash('error', 'Participant non trouvé.');
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        $participant->setEliminated(true);
        $this->entityManager->flush();

        $this->addFlash('success', "Joueur '{$participant->getPlayerName()}' éliminé !");
        return $this->redirectToRoute('app_tour_du_monde_game');
    }

    /**
     * Lancer la roue de sélection
     * Route: /tour-du-monde/game/spin-wheel
     */
    #[Route('/tour-du-monde/game/spin-wheel', name: 'app_tour_du_monde_game_spin_wheel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function spinWheel(): Response
    {
        // Vérifier les conditions pour lancer la roue
        $wheelData = $this->gameParticipantRepository->canCreateWheel();
        
        if (!$wheelData['canCreateWheel']) {
            if (!$wheelData['hasFinalist']) {
                $this->addFlash('error', 'Aucun finaliste sélectionné. Veuillez d\'abord choisir un finaliste.');
            } else {
                $this->addFlash('error', 'Aucun participant éligible pour la roue. Tous les participants sont finalistes ou éliminés.');
            }
            return $this->redirectToRoute('app_tour_du_monde_game');
        }

        // Sélectionner aléatoirement un participant éligible
        $eligibleParticipants = $wheelData['eligibleParticipants'];
        $selectedParticipant = $eligibleParticipants[array_rand($eligibleParticipants)];
        
        $this->addFlash('success', "La roue a sélectionné : {$selectedParticipant->getPlayerName()} !");
        
        return $this->redirectToRoute('app_tour_du_monde_game');
    }

    /**
     * Finir le jeu et vider les participants
     * Route: /tour-du-monde/end-game
     */
    #[Route('/tour-du-monde/end-game', name: 'app_tour_du_monde_end_game', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function endGame(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        $user = $this->getUser();
        
        // Récupérer les données avant suppression
        $gameParticipants = $this->gameParticipantRepository->findActiveParticipants();
        $session = $this->entityManager->getRepository(GameSession::class)
            ->findOneBy([
                'game' => $game,
                'masterUser' => $user,
                'status' => 'active'
            ]);

        // Sauvegarder l'historique avant suppression
        if ($session && $gameParticipants) {
            $this->saveGameHistory($session, $gameParticipants);
        }

        // Vider tous les participants de setup
        $this->participantRepository->clearAllParticipants();
        
        // Vider tous les participants de jeu
        $this->gameParticipantRepository->clearAllParticipants();
        
        // Mettre à jour la session
        if ($session) {
            $session->setStatus('completed');
            $session->setCompletedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        }

        // S'assurer qu'il n'y a plus de session active pour cet utilisateur
        $this->entityManager->getRepository(GameSession::class)
            ->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':completed')
            ->where('s.game = :game')
            ->andWhere('s.masterUser = :user')
            ->andWhere('s.status = :active')
            ->setParameter('completed', 'completed')
            ->setParameter('game', $game)
            ->setParameter('user', $user)
            ->setParameter('active', 'active')
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Jeu terminé ! L\'historique a été sauvegardé et tous les participants ont été supprimés.');
        return $this->redirectToRoute('app_tour_du_monde');
    }

    /**
     * Sauvegarde l'historique de la partie
     */
    private function saveGameHistory(GameSession $session, array $gameParticipants): void
    {
        $gameHistory = new GameHistory();
        $gameHistory->setUser($session->getMasterUser());
        $gameHistory->setGame($session->getGame());
        $gameHistory->setSessionName($session->getSessionName());
        $gameHistory->setTotalParticipants(count($gameParticipants));
        $gameHistory->setStartedAt($session->getStartedAt());
        $gameHistory->setCompletedAt(new \DateTimeImmutable());
        $gameHistory->setGameNotes($session->getNotes());

        // Calculer la durée
        if ($session->getStartedAt()) {
            $duration = $gameHistory->getCompletedAt()->getTimestamp() - $session->getStartedAt()->getTimestamp();
            $gameHistory->setTotalDurationSeconds($duration);
        }

        // Trouver le finaliste
        $finalist = $this->gameParticipantRepository->findFinalist();
        if ($finalist) {
            $gameHistory->setFinalistId($finalist->getId());
            $gameHistory->setFinalistName($finalist->getPlayerName());
        }

        // Pour le Tour du Monde, le gagnant est généralement le finaliste
        // Mais on peut aussi avoir un gagnant différent selon les règles
        $winner = $finalist; // Par défaut, le finaliste est le gagnant
        if ($winner) {
            $gameHistory->setWinnerId($winner->getId());
            $gameHistory->setWinnerName($winner->getPlayerName());
        }

        $this->entityManager->persist($gameHistory);
        $this->entityManager->flush();
    }

    /**
     * Historique des parties de l'utilisateur
     * Route: /tour-du-monde/history
     */
    #[Route('/tour-du-monde/history', name: 'app_tour_du_monde_history')]
    #[IsGranted('ROLE_USER')]
    public function history(): Response
    {
        $user = $this->getUser();
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        
        $gameHistory = $this->gameHistoryRepository->findByUserAndGame($user, $game);
        $userStats = $this->gameHistoryRepository->getUserStats($user);

        return $this->render('frontend/games/tour_du_monde/history.html.twig', [
            'gameHistory' => $gameHistory,
            'userStats' => $userStats,
            'game' => $game,
        ]);
    }

    /**
     * Reset complet pour permettre de recommencer
     * Route: /tour-du-monde/reset
     */
    #[Route('/tour-du-monde/reset', name: 'app_tour_du_monde_reset', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reset(): Response
    {
        $game = $this->gameRepository->findOneBy(['name' => 'Tour du Monde']);
        $user = $this->getUser();

        // Nettoyer tous les participants
        $this->participantRepository->clearAllParticipants();
        $this->gameParticipantRepository->clearAllParticipants();

        // Marquer toutes les sessions comme completed
        $this->entityManager->getRepository(GameSession::class)
            ->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':completed')
            ->where('s.game = :game')
            ->andWhere('s.masterUser = :user')
            ->setParameter('completed', 'completed')
            ->setParameter('game', $game)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'État du jeu réinitialisé ! Vous pouvez maintenant commencer une nouvelle partie.');
        return $this->redirectToRoute('app_tour_du_monde');
    }
}