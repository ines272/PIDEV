<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route(name: 'app_reclamation_index', methods: ['GET'])]
public function index(Request $request, ReclamationRepository $repository): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $user = $this->getUser();

    $search = $request->query->get('search');
    $statut = $request->query->get('statut');
    $priorite = $request->query->get('priorite');
    $orderBy = $request->query->get('orderBy', 'dateReclamation');
    $order = $request->query->get('order', 'DESC');

    // 🔥 Fetch only user reclamations
    $reclamations = $repository->findWithFiltersForUser(
        $user,
        $search,
        $statut,
        $priorite,
        $orderBy,
        $order
    );

    // 🔥 Compute statistics based on user data only
    $byStatut = [];
    foreach ($reclamations as $reclamation) {
        $status = $reclamation->getStatut();
        if (!isset($byStatut[$status])) {
            $byStatut[$status] = 0;
        }
        $byStatut[$status]++;
    }

    // Transform to same structure Twig expects
    $formattedStatut = [];
    foreach ($byStatut as $statutKey => $total) {
        $formattedStatut[] = [
            'statut' => $statutKey,
            'total' => $total
        ];
    }

    $stats = [
        'total' => count($reclamations),
        'byStatut' => $formattedStatut,
    ];

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
        'stats' => $stats,
        'currentSearch' => $search,
        'currentStatut' => $statut,
        'currentPriorite' => $priorite,
        'currentOrderBy' => $orderBy,
        'currentOrder' => $order,
    ]);
}



    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 🔥 SET OWNER
        $reclamation->setUser($this->getUser());

        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation créée avec succès.');
        return $this->redirectToRoute('app_reclamation_index');
    }

    return $this->render('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation modifiée avec succès.');
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_reclamation_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/admin/reclamations', name: 'app_admin_reclamation_index')]
public function adminIndex(ReclamationRepository $repository): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $reclamations = $repository->findAll();

    return $this->render('admin/reclamation/index.html.twig', [
        'reclamations' => $reclamations,
    ]);
}

}