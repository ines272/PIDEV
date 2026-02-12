<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/proprietaire')]
#[IsGranted('ROLE_PROPRIETAIRE')]
class ProprietaireController extends AbstractController
{
    #[Route('/dashboard', name: 'proprietaire_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('proprietaire/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}