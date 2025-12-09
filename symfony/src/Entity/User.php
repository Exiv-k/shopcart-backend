<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public function __construct(private array $data) {}

    public function getRoles(): array
    {
        return [$this->data['role']];
    }
    public function getPassword(): ?string
    {
        return $this->data['password_hash'];
    }
    public function getUserIdentifier(): string
    {
        return $this->data['username'];
    }

    public function getId(): ?int
    {
        return $this->data['user_id'];
    }
    public function getLastLogin(): ?string
    {
        return $this->data['last_login'];
    }

    public function eraseCredentials(): void {}
}
