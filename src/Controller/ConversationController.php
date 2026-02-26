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

    $user = $this->getUser();

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

    $message = new Message();
    $message->setConversation($conversation);
    $message->setSender($user);
    $message->setContent($content);

    $em->persist($message);
    $em->flush();

    // 🔥 DETERMINE OTHER USER
    $otherUser = $conversation->getOwner() === $user
        ? $conversation->getGardien()
        : $conversation->getOwner();

    // 🔥 NOTIFY NODE SERVER
    $httpClient->request('POST', 'http://localhost:8082/notify', [
        'json' => [
            'payload' => [
                'type' => 'chat',
                'conversationId' => $conversation->getId(),
                'content' => $content,
                'senderName' => $user->getPrenom()
            ]
        ]
    ]);

    return $this->json([
        'success' => true
    ]);
}
}