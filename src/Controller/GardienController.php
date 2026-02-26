<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostulationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gardien')]
#[IsGranted('ROLE_GARDIEN')]
class GardienController extends AbstractController
{
    #[Route('/dashboard', name: 'gardien_dashboard')]
    public function dashboard(PostulationRepository $postulationRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $postulations = $postulationRepository->findBy(
            ['applicant' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('gardien/dashboard.html.twig', [
            'user' => $user, 
            'postulations' => $postulations,
        ]);
    }
}