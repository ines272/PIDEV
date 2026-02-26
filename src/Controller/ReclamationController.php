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
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route(name: 'app_reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user     = $this->getUser();
        $search   = $request->query->get('search');
        $statut   = $request->query->get('statut');
        $priorite = $request->query->get('priorite');
        $orderBy  = $request->query->get('orderBy', 'dateReclamation');
        $order    = $request->query->get('order', 'DESC');

        $reclamations = $repository->findWithFiltersForUser(
            $user, $search, $statut, $priorite, $orderBy, $order
        );

        $byStatut = [];
        foreach ($reclamations as $reclamation) {
            $status = $reclamation->getStatut();
            if (!isset($byStatut[$status])) {
                $byStatut[$status] = 0;
            }
            $byStatut[$status]++;
        }

        $formattedStatut = [];
        foreach ($byStatut as $statutKey => $total) {
            $formattedStatut[] = ['statut' => $statutKey, 'total' => $total];
        }

        $stats = [
            'total'    => count($reclamations),
            'byStatut' => $formattedStatut,
        ];

        return $this->render('reclamation/index.html.twig', [
            'reclamations'    => $reclamations,
            'stats'           => $stats,
            'currentSearch'   => $search,
            'currentStatut'   => $statut,
            'currentPriorite' => $priorite,
            'currentOrderBy'  => $orderBy,
            'currentOrder'    => $order,
        ]);
    }

    // ✅ Une seule méthode new — fusionnée avec l'IA
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. Infos de base
            $reclamation->setUser($this->getUser());
            $reclamation->setPriorite('moyenne'); // valeur par défaut
            $reclamation->setStatut('en_attente');
            $reclamation->setDateReclamation(new \DateTime());

            $entityManager->persist($reclamation);
            $entityManager->flush();

            // 2. Détection IA de la priorité
            try {
                $apiKey = $_ENV['HF_API_KEY'];

                $prompt = "Analyse cette réclamation et réponds UNIQUEMENT par un seul mot parmi : haute, moyenne, basse

Sujet : {$reclamation->getSujet()}
Description : {$reclamation->getDescription()}

Règles :
- haute : urgence, animal blessé, danger, arnaque, absence totale du pet sitter
- moyenne : remboursement, annulation, retard, mauvaise communication
- basse : question générale, amélioration, suggestion

Réponds avec UN SEUL MOT (haute, moyenne ou basse) :";

                $response = $httpClient->request(
                    'POST',
                    'https://router.huggingface.co/v1/chat/completions',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => [
                            'model'    => 'meta-llama/Llama-3.2-3B-Instruct',
                            'messages' => [
                                ['role' => 'user', 'content' => $prompt]
                            ],
                            'max_tokens' => 10,
                        ],
                        'timeout' => 30,
                    ]
                );

                $data     = $response->toArray();
                $priorite = strtolower(trim($data['choices'][0]['message']['content']));

                // Sécurité : forcer une valeur valide
                if (!in_array($priorite, ['haute', 'moyenne', 'basse'])) {
                    $priorite = 'moyenne';
                }

                $reclamation->setPriorite($priorite);
                $entityManager->flush();

            } catch (\Exception $e) {
                // Si l'IA échoue → on garde 'moyenne' par défaut silencieusement
            }

            $this->addFlash('success', 'Réclamation soumise. Priorité détectée ');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form'        => $form,
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
        if ($reclamation->getReponses()->count() > 0) {
            $this->addFlash('error', 'Cette réclamation ne peut plus être modifiée car elle a déjà reçu une réponse.');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation modifiée avec succès.');
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form'        => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'Réclamation supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_reclamation_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->getPayload()->getString('_token'))) {
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