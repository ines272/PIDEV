<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gardien')]
#[IsGranted('ROLE_GARDIEN')]
class GardienController extends AbstractController
{
    #[Route('/dashboard', name: 'gardien_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('gardien/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}