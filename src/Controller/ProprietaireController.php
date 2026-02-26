<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostulationRepository;
use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\NotificationService;
use App\Entity\Conversation;


#[Route('/proprietaire')]
#[IsGranted('ROLE_PROPRIETAIRE')]
class ProprietaireController extends AbstractController
{
    #[Route('/dashboard', name: 'proprietaire_dashboard')]
    public function dashboard(
        PostulationRepository $postulationRepository,
        AnnouncementRepository $announcementRepository
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        // 1️⃣ Announcements created by this propriétaire
        $announcements = $announcementRepository->findBy(
            ['user' => $user],
            ['dateDebut' => 'DESC']
        );

        // 2️⃣ Postulations received on his announcements
        $postulations = $postulationRepository->findBy(
            ['owner' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('proprietaire/dashboard.html.twig', [
            'user' => $user,
            'announcements' => $announcements,
            'postulations' => $postulations,
        ]);
    }

    #[Route('/postulation/{id}/status', name: 'postulation_update_status', methods: ['POST'])]
    public function updateStatus(
        \App\Entity\Postulation $postulation,
        Request $request,
        EntityManagerInterface $em,
        NotificationService $notificationService
    ): JsonResponse {

        $user = $this->getUser();

        // 🔐 Security: only owner can update
        if ($postulation->getOwner() !== $user) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $status = $request->request->get('status');

        if (!in_array($status, ['ACCEPTED', 'REJECTED'])) {
            return $this->json(['error' => 'Invalid status'], 400);
        }

        $postulation->setStatus($status);
        $em->flush();

        /** @var User $gardien */
        $gardien = $postulation->getApplicant();

        if ($status === 'ACCEPTED') {

            $notificationService->create(
                $gardien,
                'Candidature acceptée 🎉',
                'Bonne nouvelle ! Votre candidature pour ' .
                $postulation->getAnnouncement()->getPet()->getName() .
                ' a été acceptée.',
                'success'
            );

            // 🔥 Prevent duplicate conversation
            if (!$postulation->getConversation()) {

                $conversation = new Conversation();
                $conversation->setOwner($postulation->getOwner());
                $conversation->setGardien($postulation->getApplicant());
                $conversation->setPostulation($postulation);

                // VERY IMPORTANT (bidirectional link)
                $postulation->setConversation($conversation);

                $em->persist($conversation);
                $em->flush();
            }
        } elseif ($status === 'REJECTED') {

            $notificationService->create(
                $gardien,
                'Candidature refusée ❌',
                'Votre candidature pour ' .
                $postulation->getAnnouncement()->getPet()->getName() .
                ' a été refusée.',
                'danger'
            );
        }

        return $this->json([
            'success' => true,
            'status' => $status
        ]);
    }

}