<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Account;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * Custom account provider used to apply SSO server into Symfony environment
 */
class AccountProvider implements UserProviderInterface
{
    private string $ssoHost;
    private HttpClientInterface $client;
    private SerializerInterface $serializer;
    private SessionInterface $session;

// TODO: replace httpclient, serializer and session with account repository
    public function __construct(
        HttpClientInterface $client,
        SerializerInterface $serializer,
        SessionInterface $session,
        string $ssoHost
    ) {
        $this->ssoHost = $ssoHost;
        $this->client = $client;
        $this->serializer = $serializer;
        $this->session = $session;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * @param string $token
     * @return UserInterface
     */
    public function loadUserByUsername(string $token): UserInterface
    {
        $this->session->set('jwt_token', $token);

        try {
            return $this->loadUser($token);
        } catch (Throwable $e) {
            dd($e);
        }
    }

    /**
     * @param string $token
     * @return Account|array|object
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function loadUser(string $token): Account
    {
        $url = "http://{$this->ssoHost}/api/account/current";

        $header = "Authorization: Bearer {$token}";

        $response = $this->client->request('GET', $url, ['headers' => [$header]]);
        $account = $this->serializer->deserialize($response->getContent(), Account::class, 'json');

        if (!$account instanceof Account) {
            throw new RuntimeException('Serialization failed.');
        }

        return $account;
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
     * @throws UnsupportedUserException
     * @throws Exception
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     * @param string $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return Account::class === $class;
    }
}
