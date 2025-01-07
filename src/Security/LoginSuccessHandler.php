<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_DEVELOPER', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_developer_matches'));
        } elseif (in_array('ROLE_COMPANY', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_company_dashboard'));
        }

        // Redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
