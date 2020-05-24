<?php

declare(strict_types=1);

namespace App\Repository\Api;

use App\Model\Account;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Repository used to fetch user correctly
 */
class AccountRepository extends AbstractApiRepository
{
    private const CURRENT_USER_ENDPOINT = 'http://192.168.42.86:8000/api/account/current';

    /**
     * @param string $token
     * @return Account
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findUserByToken(string $token): Account
    {
        $account = $this->fetchEndpoint(self::CURRENT_USER_ENDPOINT, Account::class, 'POST', null, $token);
        dd($account);

        return $account;
    }
}
