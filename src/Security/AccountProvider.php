<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Account;
use App\Repository\Api\AccountRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

/**
 * Custom account provider used to apply SSO server into Symfony environment
 */
class AccountProvider implements UserProviderInterface
{
    private SessionInterface $session;
    private AccountRepository $repository;

    public function __construct(SessionInterface $session, AccountRepository $repository)
    {
        $this->session = $session;
        $this->repository = $repository;
    }

    /** @inheritDoc */
    public function loadUserByUsername(string $token): UserInterface
    {
        return $this->fetchUser();
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @param UserInterface $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): ?UserInterface
    {
        return $this->fetchUser();
    }

    /**
     * Tells Symfony to use this provider for this User class.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return Account::class === $class;
    }

    /**
     * Fetches user from Cloud SSO API
     *
     * @return Account|null
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchUser(): ?Account
    {
        try {
            $account = $this->repository->findUserByToken($this->session->get('jwt_token'));
        } catch (AuthenticationException $e) {
            // this never happens on user load, but may happen on user refresh (if JWT token is outdated)
            $this->refreshToken($this->session->get('refresh_token'));
            $account = $this->repository->findUserByToken($this->session->get('jwt_token'));
        } catch (Throwable $e) {
            dd($e);
        } finally {
            // returns account if token is valid, if token is refreshed and null if refreshment didn't went well
            return $account;
        }
    }

    /**
     * Sets new pair of tokens in session
     *
     * Called when request with current token returned with status 401
     *
     * @param string $refresh
     */
    private function refreshToken(string $refresh): void
    {
        $response = $this->repository->obtainNewToken($refresh);

        if ($response !== null) {
            $this->session->set('jwt_token', $response->getToken());
            $this->session->set('refresh_token', $response->getRefreshToken());
        }
    }
}
