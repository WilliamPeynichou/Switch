<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PasswordResetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private UserService $userService,
        private MailerInterface $mailer
    ) {}

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Veuillez entrer une adresse email valide.');
                return $this->render('security/forgot_password.html.twig');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);
            
            if (!$user) {
                // Ne pas révéler si l'email existe ou non
                $this->addFlash('success', 'Si cette adresse email existe dans notre système, vous recevrez un email de réinitialisation.');
                return $this->redirectToRoute('app_forgot_password');
            }

            // Invalider les anciens tokens
            $this->passwordResetTokenRepository->invalidateUserTokens($email);

            // Créer un nouveau token
            $token = new PasswordResetToken();
            $token->setEmail($email);
            $this->entityManager->persist($token);
            $this->entityManager->flush();

            // Envoyer l'email de réinitialisation
            $this->sendPasswordResetEmail($user, $token);

            $this->addFlash('success', 'Si cette adresse email existe dans notre système, vous recevrez un email de réinitialisation.');
            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request): Response
    {
        $resetToken = $this->passwordResetTokenRepository->findValidToken($token);
        
        if (!$resetToken) {
            $this->addFlash('error', 'Ce lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', '');
            $confirmPassword = $request->request->get('confirm_password', '');

            if (empty($password) || strlen($password) < 6) {
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 6 caractères.');
                return $this->render('security/reset_password.html.twig', ['token' => $token]);
            }

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('security/reset_password.html.twig', ['token' => $token]);
            }

            // Trouver l'utilisateur
            $user = $this->userRepository->findOneBy(['email' => $resetToken->getEmail()]);
            
            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_forgot_password');
            }

            // Mettre à jour le mot de passe
            $this->userService->updateUserPassword($user, $password);
            
            // Marquer le token comme utilisé
            $resetToken->setIsUsed(true);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }

    private function sendPasswordResetEmail(User $user, PasswordResetToken $token): void
    {
        $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token->getToken()], true);

        $email = (new Email())
            ->from('noreply@switch-game.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe - Switch Game')
            ->html($this->renderView('emails/password_reset.html.twig', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'expiresAt' => $token->getExpiresAt()
            ]));

        $this->mailer->send($email);
    }
}
