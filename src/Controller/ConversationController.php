<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\User;


class ConversationController extends AbstractController
{
    #[Route('/conversation/{id}', name: 'conversation_show')]
    public function show(
        Conversation $conversation,
        MessageRepository $messageRepository
    ): Response {

        $user = $this->getUser();

        // SECURITY: must belong to conversation
        if (
            $conversation->getOwner() !== $user &&
            $conversation->getGardien() !== $user
        ) {
            throw $this->createAccessDeniedException();
        }

        $messages = $messageRepository->findBy(
            ['conversation' => $conversation],
            ['createdAt' => 'ASC']
        );

        return $this->render('chat/show.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }
    #[Route('/conversation/{id}/send', name: 'conversation_send', methods: ['POST'])]
public function sendMessage(
    Conversation $conversation,
    Request $request,
    EntityManagerInterface $em,
    HttpClientInterface $httpClient
): JsonResponse {

    /** @var User $user */
    $user = $this->getUser();

    // 🔐 Security check
    if (
        $conversation->getOwner() !== $user &&
        $conversation->getGardien() !== $user
    ) {
        return $this->json(['error' => 'Unauthorized'], 403);
    }

    $content = trim($request->request->get('content'));

    if (!$content) {
        return $this->json(['error' => 'Empty message'], 400);
    }

    // 📝 Create message
    $message = new Message();
    $message->setConversation($conversation);
    $message->setSender($user);
    $message->setContent($content);
    // ❌ DO NOT set createdAt — constructor already does it

    $em->persist($message);
    $em->flush();

    // 👤 Determine receiver
    $receiver = $conversation->getOwner() === $user
        ? $conversation->getGardien()
        : $conversation->getOwner();

    // 🚀 Notify WebSocket bridge
    try {
        $httpClient->request('POST', 'http://localhost:8082/notify', [
    'json' => [
        'targetUserId' => $receiver->getId(),   // 🔥 THIS IS THE FIX
        'payload' => [
            'type' => 'chat',
            'conversationId' => $conversation->getId(),
            'senderName' => $user->getPrenom(),
            'content' => $content,
            'time' => $message->getCreatedAt()->format('H:i')
        ]
    ]
]);
    } catch (\Exception $e) {
        // Optional: log error
    }

    return $this->json([
        'success' => true
    ]);
}
}