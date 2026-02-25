<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\NotificationService;
use App\Entity\Announcement;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

use App\Entity\Postulation;
use App\Repository\PostulationRepository;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_landing');
        }

        // Fetch latest announcements
        $announcements = $announcementRepository->findBy(
            [],
            ['dateDebut' => 'DESC']
        );

        return $this->render('front/home/index.html.twig', [
            'announcements' => $announcements
        ]);
    }

    #[Route('/postulation/{id}/postuler', name: 'announcement_postuler', methods: ['POST'])]
    public function postuler(
        Announcement $announcement,
        EntityManagerInterface $em,
        NotificationService $notificationService,
        PostulationRepository $postulationRepository
    ): JsonResponse {

        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        // ❌ Prevent applying to own announcement
        if ($announcement->getUser() === $user) {
            return $this->json(['error' => 'You cannot apply to your own announcement'], 400);
        }

        // ❌ Prevent duplicate application
        $existing = $postulationRepository->findOneBy([
            'applicant' => $user,
            'announcement' => $announcement
        ]);

        if ($existing) {
            return $this->json(['error' => 'You already applied'], 400);
        }

        // ✅ Create Postulation
        $postulation = new Postulation();
        $postulation->setApplicant($user);
        $postulation->setAnnouncement($announcement);
        $postulation->setOwner($announcement->getUser());

        $em->persist($postulation);
        $em->flush();

        // 🔔 Notify Owner
        $notificationService->create(
            $announcement->getUser(),
            'Nouvelle candidature 📩',
            $user->getFullName() . ' a postulé pour votre annonce.',
            'info'
        );

        return $this->json(['success' => true]);
    }
}