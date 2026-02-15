<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PROPRIETAIRE')]
class ProprietaireController extends AbstractController
{
    #[Route('/proprietaire/dashboard', name: 'proprietaire_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();

        return $this->render('proprietaire/dashboard.html.twig', [
            'user' => $user,
        ]);
    }
}