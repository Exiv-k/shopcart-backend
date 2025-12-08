<?php

namespace App\Security;

use App\Service\Database;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class DatabaseUserProvider implements UserProviderInterface
{
    public function __construct(private Database $db) {}

    public function loadUserByIdentifier(string $username): UserInterface
    {
        $user = $this->db->fetchOne(
            'SELECT user_id, username, password_hash, role FROM users WHERE username = :u',
            [':u' => $username]
        );

        if (!$user) {
            throw new UserNotFoundException("User $username not found.");
        }

        return new User($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}
