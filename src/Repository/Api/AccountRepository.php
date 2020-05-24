<?php

declare(strict_types=1);

namespace App\Repository\Api;

use App\Model\Account;
use App\Model\RefreshTokenResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

/**
 * Repository used to fetch user correctly
 */
class AccountRepository extends AbstractApiRepository
{
    private const CURRENT_USER_ENDPOINT = 'http://192.168.42.86:8000/api/account/current';
    private const REFRESH_TOKEN_ENDPOINT = 'http://192.168.42.86:8000/api/login/refresh';

    /**
     * @param string $token
     * @return Account
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws AuthenticationException
     */
    public function findUserByToken(string $token): ?Account
    {
        try {
            return $this->fetchEndpoint(
                self::CURRENT_USER_ENDPOINT,
                Account::class,
                'POST',
                null,
                $token
            );
        } catch (ClientExceptionInterface $exception) {
            if ($exception->getCode() === 401) {
                throw new AuthenticationException('User token is not valid or outdated');
            }
        }
    }

    /**
     * @param string $refreshToken
     */
    public function obtainNewToken(string $refreshToken): ?RefreshTokenResponse
    {
        dd($refreshToken);
        try {
            return $this->fetchEndpoint(
                self::REFRESH_TOKEN_ENDPOINT,
                RefreshTokenResponse::class,
                'POST',
                null,
                null,
                ['refresh_token' => $refreshToken]
            );
        } catch (Throwable $e) {
            return null;
        }
    }
}
