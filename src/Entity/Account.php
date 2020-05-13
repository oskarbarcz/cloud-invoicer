<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

class Account
{
    private ?string $uuiid = null;
    private ?string $name = null;
    private ?string $surname = null;
    private ?string $email = null;
    private ?array $roles = [];
    private ?DateTime $createdAt = null;

    public function getUuiid(): ?string
    {
        return $this->uuiid;
    }

    public function setUuiid(?string $uuiid): Account
    {
        $this->uuiid = $uuiid;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Account
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): Account
    {
        $this->surname = $surname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Account
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): Account
    {
        $this->roles = $roles;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): Account
    {
        $this->createdAt = $createdAt;
        return $this;
    }


}
