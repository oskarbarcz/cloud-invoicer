<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Handles logging user in via SSO server
 */
class SsoAuthCustomer extends AbstractGuardAuthenticator
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /** @inheritDoc */
    public function supports(Request $request): bool
    {
        return $request->get('_route') === 'app_sso-login' && $request->get('token') !== null;
    }

    /** @inheritDoc */
    public function getCredentials(Request $request): array
    {
        return ['token' => $request->get('token')];
    }

    /** @inheritDoc */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        return $userProvider->loadUserByUsername($credentials['token']);
    }

    /** @inheritDoc */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true; // no credintials can be checked here
    }

    /** @inheritDoc */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // todo redirect to some nice error screen
    }

    /** @inheritDoc */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        $url = $this->urlGenerator->generate('app_login-success');
        return new RedirectResponse($url);
    }

    /** @inheritDoc */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $url = $this->urlGenerator->generate('app_sso-login');
        return new RedirectResponse($url);
    }

    /** @inheritDoc */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
