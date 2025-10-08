<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur principal pour l'authentification
 * Gère : inscription, connexion, déconnexion, profil
 */
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserService $userService
    ) {}

    /**
     * Page d'inscription
     * Route: /register
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->registerUser($user, $form->get('plainPassword')->getData());
            $this->addFlash('success', 'Votre compte a été créé avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Page de connexion
     * Route: /login
     */
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * Déconnexion
     * Route: /logout
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut être vide - elle sera interceptée par la clé de déconnexion de votre firewall.');
    }

    /**
     * Page de profil utilisateur
     * Route: /profile
     */
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Édition du profil utilisateur
     * Route: /profile/edit
     */
    #[Route('/profile/edit', name: 'app_profile_edit')]
    #[IsGranted('ROLE_USER')]
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->updateUserProfile($user, $form->get('plainPassword')->getData());
            $this->addFlash('success', 'Votre profil a été mis à jour !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/edit_profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
