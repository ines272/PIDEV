<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class NotificationService
{
    private EntityManagerInterface $em;
    private HttpClientInterface $httpClient;

    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $httpClient
    ) {
        $this->em = $em;
        $this->httpClient = $httpClient;
    }

    public function create(
        User $user,
        string $title,
        string $message,
        string $type,
        ?string $actionUrl = null,
        string $priority = 'normal'
    ): Notification
    {
        $notification = new Notification();

        $notification
            ->setUser($user)
            ->setTitle($title)
            ->setMessage($message)
            ->setType($type)
            ->setActionUrl($actionUrl)
            ->setPriority($priority);

        $this->em->persist($notification);
        $this->em->flush();

        // 🔥 SEND TO WEBSOCKET SERVER
        try {
            $this->httpClient->request('POST', 'http://localhost:8082/notify', [
                'json' => [
                    'targetUserId' => $user->getId(),
                    'payload' => [
                        'title' => $title,
                        'message' => $message
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            // ignore errors for now
        }

        return $notification;
    }
}