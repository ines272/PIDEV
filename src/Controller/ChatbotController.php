<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient) {}

    #[Route('/api/chatbot', name: 'chatbot_message', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        try {
            $data        = json_decode($request->getContent(), true);
            $userMessage = $data['message'] ?? '';

            if (empty($userMessage)) {
                return new JsonResponse(['reply' => 'Message vide.', 'show_map' => false]);
            }

            $userRoles    = $this->getUser()?->getRoles() ?? ['ROLE_VISITOR'];
            $systemPrompt = $this->buildSystemPrompt($userRoles);
            $apiKey       = $_ENV['HUGGINGFACE_API_KEY'] ?? $_ENV['HF_API_KEY'] ?? '';

            if (empty($apiKey)) {
                return new JsonResponse([
                    'reply'    => 'Cle API HuggingFace manquante dans .env',
                    'show_map' => false,
                ]);
            }

            // ✅ Construction manuelle du body JSON pour éviter les problèmes d'encodage
            $body = json_encode([
                'model' => 'meta-llama/Llama-3.2-3B-Instruct',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userMessage],
                ],
                'max_tokens'  => 300,
                'temperature' => 0.7,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $response = $this->httpClient->request('POST',
                'https://router.huggingface.co/hf-inference/v1/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'body' => json_encode([
                        'model'    => 'meta-llama/Llama-3.2-3B-Instruct',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user',   'content' => $userMessage],
                        ],
                        'max_tokens'  => 300,
                        'temperature' => 0.7,
                    ], JSON_UNESCAPED_UNICODE),
                    'timeout' => 30,
                ]
            );

            $statusCode = $response->getStatusCode();

                            if ($statusCode === 503) {
                                return new JsonResponse([
                                    'reply'    => 'Le modele IA est en cours de chargement, reessayez dans 20 secondes.',
                                    'show_map' => false,
                                ]);
                            }

                            $rawContent = $response->getContent(false); // récupère le texte brut
                $result     = json_decode($rawContent, true);

                // Debug temporaire — à supprimer après
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return new JsonResponse([
                        'reply'    => 'Réponse brute HF : ' . substr($rawContent, 0, 300),
                        'show_map' => false,
                    ]);
                }

$botReply = $result['choices'][0]['message']['content']
    ?? $result['error']
    ?? "Je n'ai pas pu traiter votre demande.";

            return new JsonResponse([
                'reply'    => trim($botReply),
                'show_map' => $this->detectMapIntent($userMessage),
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'reply'    => 'Erreur: ' . $e->getMessage(),
                'show_map' => false,
            ], 200);
        }
    }

    #[Route('/api/gardiens/nearby', name: 'gardiens_nearby', methods: ['GET'])]
    public function nearbyGuardians(Request $request, UserRepository $repo): JsonResponse
    {
        try {
            $lat      = (float) $request->query->get('lat', 36.8065);
            $lng      = (float) $request->query->get('lng', 10.1815);
            $radius   = (float) $request->query->get('radius', 10);
            $gardiens = $repo->findNearbyGuardians($lat, $lng, $radius);

            return new JsonResponse(array_map(fn($u) => [
                'id'        => $u->getId(),
                'nom'       => $u->getPrenom() . ' ' . $u->getNom(),
                'latitude'  => $u->getLatitude(),
                'longitude' => $u->getLongitude(),
                'rating'    => 4.5,
            ], $gardiens));

        } catch (\Exception $e) {
            return new JsonResponse([]);
        }
    }

    private function buildSystemPrompt(array $roles): string
    {
        $roleContext = in_array('ROLE_GARDIEN', $roles)
            ? "L'utilisateur est un GARDIEN."
            : (in_array('ROLE_PROPRIETAIRE', $roles)
                ? "L'utilisateur est un PROPRIETAIRE."
                : "L'utilisateur n'est pas encore connecte.");

        return "Tu es l'assistant virtuel de SitMyPet, une plateforme de garde d'animaux de compagnie. "
             . "But : Mettre en contact des proprietaires d'animaux avec des gardiens professionnels. "
             . "L'admin garantit une remuneration transparente et une verification rigoureuse des gardiens. "
             . "Les gardiens doivent uploader leur certificat PDF pour validation par l'admin avant de pouvoir operer. "
             . "ROLES - PROPRIETAIRE peut : ajouter ses animaux et chercher un gardien, s'inscrire a des evenements, soumettre des reclamations. "
             . "ROLES - GARDIEN peut : postuler sur des annonces, organiser des evenements, doit attendre la validation de son certificat. "
             . "NAVIGATION : /register = Inscription, /login = Connexion, /annonces = Annonces, /evenements = Evenements, /reclamations = Reclamations. "
             . "CONTEXTE : " . $roleContext . " "
             . "Reponds en francais, de facon concise et utile. Si l'utilisateur demande des gardiens proches, indique que la carte s'affichera.";
    }

    private function detectMapIntent(string $message): bool
    {
        $mapKeywords = ['pres', 'proche', 'autour', 'localisation', 'carte', 'map', 'quartier', 'ville'];
        $message     = strtolower($message);

        foreach ($mapKeywords as $keyword) {
            if (str_contains($message, $keyword)) return true;
        }

        return false;
    }
}
