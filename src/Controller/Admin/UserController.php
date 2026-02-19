<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $search = $request->query->get('search', '');
        $role = $request->query->get('role', '');
        $status = $request->query->get('status', '');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($search) {
            $queryBuilder->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
                        ->setParameter('search', '%' . $search . '%');
        }

        if ($role) {
            $queryBuilder->andWhere('u.role = :role')
                        ->setParameter('role', $role);
        }

        if ($status === 'active') {
            $queryBuilder->andWhere('u.isActive = true')
                        ->andWhere('u.deletedAt IS NULL');
        } elseif ($status === 'inactive') {
            $queryBuilder->andWhere('u.isActive = false');
        } elseif ($status === 'deleted') {
            $queryBuilder->andWhere('u.deletedAt IS NOT NULL');
        } else {
            $queryBuilder->andWhere('u.deletedAt IS NULL');
        }

        $queryBuilder->orderBy('u.createdAt', 'DESC');
        $users = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $temporaryPassword = bin2hex(random_bytes(8));
            $hashedPassword = $passwordHasher->hashPassword($user, $temporaryPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Utilisateur créé avec succès. Mot de passe temporaire : %s',
                $temporaryPassword
            ));

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $user->setDeletedAt(new \DateTimeImmutable());
            $user->setIsActive(false);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/restore', name: 'app_admin_user_restore', methods: ['POST'])]
    public function restore(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('restore'.$user->getId(), $request->request->get('_token'))) {
            $user->setDeletedAt(null);
            $user->setIsActive(true);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur restauré avec succès.');
        }
        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_user_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('toggle'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsActive(!$user->getIsActive());
            $entityManager->flush();
            $status = $user->getIsActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Utilisateur $status avec succès.");
        }
        return $this->redirectToRoute('app_admin_user_index');
    }
}