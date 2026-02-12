<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // =========================================================
    // LOGIN
    // =========================================================
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si déjà connecté → rediriger selon le rôle
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_redirect');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // =========================================================
    // LOGOUT
    // =========================================================
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Intercepté automatiquement par le firewall Symfony
        throw new \LogicException('This method can be blank.');
    }

    // =========================================================
    // REGISTER
    // =========================================================
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Si déjà connecté → rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard_redirect');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);

            // 2. Définir les roles Symfony (tableau)
            $user->setRoles([$user->getRole()]);

            // 3. Activer le compte
            $user->setIsActive(true);

            // 4. Sauvegarder en base
            $entityManager->persist($user);
            $entityManager->flush();

            // 5. Message flash et redirection vers login
            $this->addFlash('success', '✅ Compte créé avec succès ! Connectez-vous.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // =========================================================
    // REDIRECTION APRÈS LOGIN (selon le rôle)
    // =========================================================
    #[Route('/dashboard-redirect', name: 'app_dashboard_redirect')]
    public function dashboardRedirect(): Response
    {
        $user = $this->getUser();

        // Pas connecté → login
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Admin → backoffice
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Propriétaire → espace front propriétaire
        if ($this->isGranted('ROLE_PROPRIETAIRE')) {
            return $this->redirectToRoute('proprietaire_dashboard');
        }

        // Gardien → espace front gardien
        if ($this->isGranted('ROLE_GARDIEN')) {
            return $this->redirectToRoute('gardien_dashboard');
        }

        // Par défaut → page d'accueil
        return $this->redirectToRoute('app_home');
    }
}