<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AnnouncementController extends AbstractController
{
    #[Route('/announcement', name: 'app_announcement')]
public function index(Request $request, AnnouncementRepository $repository): Response
{
    $address = $request->query->get('address');
    $dateDebut = $request->query->get('dateDebut');
    $dateFin = $request->query->get('dateFin');

    $announcements = $repository->searchByCriteria(
        $address,
        $dateDebut,
        $dateFin
    );

    return $this->render('announcement/index.html.twig', [
        'announcements' => $announcements,
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
        $announcement = new Announcement();

        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        if ($this->isCsrfTokenValid('delete_announcement_'.$announcement->getId(), $request->request->get('_token'))) {
            $em->remove($announcement);
            $em->flush();
        }

        return $this->redirectToRoute('app_announcement');
    }
}
