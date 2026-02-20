<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        
        // Si c'est l'utilisateur ID 1, rediriger vers l'admin
        if ($user->getId() === 1) {
            return new RedirectResponse($this->router->generate('app_user_index'));
        }

        // Sinon, utilisateur normal va vers home
        return new RedirectResponse($this->router->generate('app_home'));
    }
}