<?php

namespace App\Controller;

use App\Entity\Reclamation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/admin/ai')]
class AiSuggestionController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient) {}

    #[Route('/suggest/{id}', name: 'admin_ai_suggest', methods: ['GET'])]
public function suggest(Reclamation $reclamation): JsonResponse
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $apiKey = $_ENV['HF_API_KEY'];

    $prompt = "Tu es un agent de support client pour SitMyPet, une plateforme de pet sitting.
    Un client nommé {$reclamation->getNomClient()} a soumis une réclamation.
    Sujet : {$reclamation->getSujet()}
    Rédige une réponse professionnelle et empathique en français, 3-4 phrases max.
    Commence obligatoirement par : Bonjour {$reclamation->getNomClient()},";

    try {
        $response = $this->httpClient->request(
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
                    'max_tokens' => 300,
                ],
                'timeout' => 30,
            ]
        );

        $data       = $response->toArray();
        $suggestion = $data['choices'][0]['message']['content'];

        return new JsonResponse(['suggestion' => trim($suggestion)]);

    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 500);
    }
}
#[Route('/priority/{id}', name: 'admin_ai_priority', methods: ['GET'])]
public function detectPriority(Reclamation $reclamation): JsonResponse
{
    $apiKey = $_ENV['HF_API_KEY'];

    $prompt = "Analyse cette réclamation et réponds UNIQUEMENT par un seul mot parmi : haute, moyenne, basse
    
Sujet : {$reclamation->getSujet()}
Description : {$reclamation->getDescription()}

Règles :
- haute : urgence, animal blessé, danger, arnaque, absence totale du pet sitter
- moyenne : remboursement, annulation, retard, mauvaise communication  
- basse : question générale, amélioration, suggestion

Réponds avec UN SEUL MOT (haute, moyenne ou basse) :";

    try {
        $response = $this->httpClient->request(
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
        $result   = strtolower(trim($data['choices'][0]['message']['content']));

        
        if (!in_array($result, ['haute', 'moyenne', 'basse'])) {
            $result = 'moyenne';
        }

        return new JsonResponse(['priorite' => $result]);

    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 500);
    }
}
}