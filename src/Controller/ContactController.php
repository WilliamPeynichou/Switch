<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Repository\ContactMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactMessageRepository $contactMessageRepository,
        private MailerInterface $mailer
    ) {}

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $email = trim($request->request->get('email', ''));
            $subject = trim($request->request->get('subject', ''));
            $message = trim($request->request->get('message', ''));

            // Validation
            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->render('contact/contact.html.twig');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'L\'adresse email n\'est pas valide.');
                return $this->render('contact/contact.html.twig');
            }

            // Créer le message
            $contactMessage = new ContactMessage();
            $contactMessage
                ->setName($name)
                ->setEmail($email)
                ->setSubject($subject)
                ->setMessage($message);

            $this->entityManager->persist($contactMessage);
            $this->entityManager->flush();

            // Envoyer l'email
            $this->sendContactEmail($contactMessage);

            $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons bientôt.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig');
    }

    private function sendContactEmail(ContactMessage $contactMessage): void
    {
        $email = (new Email())
            ->from('noreply@switch-game.com')
            ->to('williampro1711@gmail.com')
            ->subject('[Switch Game] Nouveau message de contact')
            ->html($this->renderView('emails/contact_admin.html.twig', [
                'contactMessage' => $contactMessage
            ]));

        $this->mailer->send($email);
    }

    #[Route('/contact/response/{id}', name: 'app_contact_response', methods: ['POST'])]
    public function respondToContact(int $id, Request $request): Response
    {
        $contactMessage = $this->contactMessageRepository->find($id);
        
        if (!$contactMessage) {
            $this->addFlash('error', 'Message de contact non trouvé.');
            return $this->redirectToRoute('app_admin_contacts');
        }

        $response = trim($request->request->get('response', ''));
        
        if (empty($response)) {
            $this->addFlash('error', 'La réponse ne peut pas être vide.');
            return $this->redirectToRoute('app_admin_contacts');
        }

        // Marquer comme lu
        $contactMessage->setIsRead(true);
        $contactMessage->setReadAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        // Envoyer la réponse
        $this->sendResponseEmail($contactMessage, $response);

        $this->addFlash('success', 'Réponse envoyée avec succès !');
        return $this->redirectToRoute('app_admin_contacts');
    }

    private function sendResponseEmail(ContactMessage $contactMessage, string $response): void
    {
        $email = (new Email())
            ->from('williampro1711@gmail.com')
            ->to($contactMessage->getEmail())
            ->subject('Réponse à votre message - Switch Game')
            ->html($this->renderView('emails/contact_response.html.twig', [
                'contactMessage' => $contactMessage,
                'response' => $response
            ]));

        $this->mailer->send($email);
    }

    #[Route('/admin/contacts', name: 'app_admin_contacts')]
    public function adminContacts(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $messages = $this->contactMessageRepository->findRecentMessages(50);

        return $this->render('admin/contacts.html.twig', [
            'messages' => $messages
        ]);
    }
}
