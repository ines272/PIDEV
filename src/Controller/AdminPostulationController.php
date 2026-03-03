<?php
// src/Controller/AdminPostulationController.php

namespace App\Controller;

use App\Entity\Postulation;
use App\Repository\PostulationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/postulations')]
class AdminPostulationController extends AbstractController
{
    #[Route('', name: 'admin_postulation_index')]
    public function index(PostulationRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postulations = $repository->findBy([], ['createdAt' => 'DESC']);

        $stats = [
            'total'    => count($postulations),
            'pending'  => count(array_filter($postulations, fn($p) => $p->getStatus() === 'PENDING')),
            'accepted' => count(array_filter($postulations, fn($p) => $p->getStatus() === 'ACCEPTED')),
            'rejected' => count(array_filter($postulations, fn($p) => $p->getStatus() === 'REJECTED')),
        ];

        return $this->render('admin/postulation/index.html.twig', [
            'postulations' => $postulations,
            'stats'        => $stats,
        ]);
    }

    #[Route('/{id}/accept', name: 'admin_postulation_accept', methods: ['POST'])]
    public function accept(Postulation $postulation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postulation->accept();
        $em->flush();

        $this->addFlash('success', 'Postulation acceptée avec succès.');
        return $this->redirectToRoute('admin_postulation_index');
    }

    #[Route('/{id}/reject', name: 'admin_postulation_reject', methods: ['POST'])]
    public function reject(Postulation $postulation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postulation->reject();
        $em->flush();

        $this->addFlash('success', 'Postulation refusée.');
        return $this->redirectToRoute('admin_postulation_index');
    }

    #[Route('/{id}/delete', name: 'admin_postulation_delete', methods: ['POST'])]
    public function delete(Postulation $postulation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($postulation);
        $em->flush();

        $this->addFlash('success', 'Postulation supprimée.');
        return $this->redirectToRoute('admin_postulation_index');
    }
}