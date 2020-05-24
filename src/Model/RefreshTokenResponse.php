<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Response from RefreshToken endpoint
 */
class RefreshTokenResponse
{
    private ?string $refreshToken = null;
    private ?string $token = null;

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): RefreshTokenResponse
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): RefreshTokenResponse
    {
        $this->token = $token;
        return $this;
    }
}
