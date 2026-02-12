<?php
namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        $totalUsers = $userRepository->count([]);
        $activeUsers = $userRepository->count(['isActive' => true, 'deletedAt' => null]);
        $proprietaires = $userRepository->count(['role' => 'ROLE_PROPRIETAIRE', 'deletedAt' => null]);
        $gardiens = $userRepository->count(['role' => 'ROLE_GARDIEN', 'deletedAt' => null]);

        $latestUsers = $userRepository->findBy(
            ['deletedAt' => null],
            ['createdAt' => 'DESC'],
            5
        );

        return $this->render('admin/dashboard/index.html.twig', [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'proprietaires' => $proprietaires,
            'gardiens' => $gardiens,
            'latest_users' => $latestUsers,
        ]);
    }
}