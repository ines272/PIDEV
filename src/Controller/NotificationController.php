<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

#[Route('/notifications')]
// #[IsGranted('IS_AUTHENTICATED_FULLY')]
class NotificationController extends AbstractController
{

#[Route('/test-realtime', name: 'notification_test_realtime', methods: ['GET'])]
public function testRealtime(
    NotificationService $notificationService,
    EntityManagerInterface $em
): Response {

    $user = $em->getRepository(User::class)->find(1);

    if (!$user) {
        return new Response('User not found');
    }

    $notificationService->create(
        $user,
        'Realtime Test 🔔',
        'This notification is sent instantly!',
        'info'
    );

    return new Response('Notification sent');
}

    #[Route('/latest', name: 'notification_latest', methods: ['GET'])]
    public function latest(NotificationRepository $repository): JsonResponse
    {
        $notifications = $repository->findLatestForUser(
            $this->getUser(),
            5
        );

        return $this->json(
            $notifications,
            200,
            [],
            ['groups' => ['notification:read']]
        );
    }

    #[Route('/count-unread', name: 'notification_count_unread', methods: ['GET'])]
    public function countUnread(NotificationRepository $repository): JsonResponse
    {
        $count = $repository->countUnread($this->getUser());

        return $this->json(['count' => $count]);
    }

    #[Route('/{id}/mark-read', name: 'notification_mark_read', methods: ['POST'])]
    public function markRead(
        int $id,
        NotificationRepository $repository,
        EntityManagerInterface $em
    ): JsonResponse {

        $notification = $repository->find($id);

        if (!$notification || $notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();
        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/mark-all-read', name: 'notification_mark_all', methods: ['POST'])]
    public function markAll(
        NotificationRepository $repository
    ): JsonResponse {

        $repository->markAllAsRead($this->getUser());

        return $this->json(['success' => true]);
    }
}