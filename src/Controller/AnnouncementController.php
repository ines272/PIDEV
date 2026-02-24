<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepository;
use App\Repository\PetRepository;
use App\Enum\TypeGuard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AnnouncementController extends AbstractController
{
    #[Route('/announcement', name: 'app_announcement')]
    public function index(
        Request $request,
        AnnouncementRepository $repository,
        PetRepository $petRepository
    ): Response {
        $address = $request->query->get('address');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');

        $announcements = $repository->searchByCriteria(
            $address,
            $dateDebut,
            $dateFin
        );

        $user = $this->getUser();

        $pets = [];
        if ($user) {
            $pets = $petRepository->findByOwner($user);
        }

        return $this->render('announcement/index.html.twig', [
            'announcements' => $announcements,
            'pets' => $pets,
        ]);
    }

    #[Route('/announcement/filter', name: 'app_announcement_filter', methods: ['GET'])]
    public function filter(Request $request, AnnouncementRepository $repository): Response
    {
        $address = $request->query->get('address');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');

        $announcements = $repository->searchByCriteria(
            $address,
            $dateDebut,
            $dateFin
        );

        return $this->render('announcement/_table.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    #[Route('/announcement/new', name: 'app_announcement_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $announcement = new Announcement();
        $announcement->setUser($this->getUser());

        $form = $this->createForm(AnnouncementType::class, $announcement, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Utilisation de l'import (sans backslash)
            if ($announcement->getCareType() !== TypeGuard::CHEZ_MOI) {
                $announcement->setVisitPerDay(null);
                $announcement->setVisitHours([]);
            }

            $em->persist($announcement);
            $em->flush();

            return $this->redirectToRoute('app_announcement');
        }

        return $this->render('announcement/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/announcement/{id}/edit', name: 'app_announcement_edit')]
    public function edit(Announcement $announcement, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($announcement->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AnnouncementType::class, $announcement, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
   

            // ✅ Utilisation de l'import
            if ($announcement->getCareType() !== TypeGuard::CHEZ_MOI) {
                $announcement->setVisitPerDay(null);
                $announcement->setVisitHours([]);
            }

            $em->flush();

            return $this->redirectToRoute('app_announcement');
        }

        return $this->render('announcement/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }

    #[Route('/announcement/{id}/delete', name: 'app_announcement_delete', methods: ['POST'])]
    public function delete(Announcement $announcement, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_announcement_' . $announcement->getId(), $request->request->get('_token'))) {
            $em->remove($announcement);
            $em->flush();
        }

        return $this->redirectToRoute('app_announcement');
    }

    #[Route('/announcement-admin/{id}/delete', name: 'app_announcement_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Announcement $announcement, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_announcement_' . $announcement->getId(), $request->request->get('_token'))) {
            $em->remove($announcement);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_announcement_index');
    }

    #[Route('/admin/announcements', name: 'app_admin_announcement_index')]
    public function adminIndex(AnnouncementRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $announcements = $repository->findAll();

        return $this->render('admin/announcement/index.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    #[Route('/admin/announcement/{id}/edit', name: 'app_admin_announcement_edit', methods: ['POST'])]
    public function adminEdit(Announcement $announcement, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $announcement->setAddress($request->request->get('address'));
        // ✅ Utilisation de l'import avec from()
        $announcement->setCareType(TypeGuard::from($request->request->get('careType')));
        $announcement->setDateDebut(new \DateTime($request->request->get('dateDebut')));
        $announcement->setDateFin(new \DateTime($request->request->get('dateFin')));
        $announcement->setVisitPerDay((int) $request->request->get('visitPerDay'));
        $announcement->setRenumerationMin((float) $request->request->get('renumerationMin'));
        $announcement->setRenumerationMax((float) $request->request->get('renumerationMax'));

        $horaires = $request->request->all('horaire');
        $announcement->setVisitHours($horaires);

        // ✅ Utilisation de l'import
        if ($announcement->getCareType() !== TypeGuard::CHEZ_MOI) {
            $announcement->setVisitPerDay(null);
            $announcement->setVisitHours([]);
        }

        $em->flush();

        return $this->redirectToRoute('app_admin_announcement_index');
    }
}