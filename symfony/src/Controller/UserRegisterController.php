<?php

namespace App\Controller;

use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


class UserRegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, Database $db): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        if ($username === '' || $password === '') {
            return $this->json(['error' => 'Fields missing'], 400);
        }
        if (strlen($password) < 6) {
            return $this->json(['error' => 'Password too short'], 400);
        }

        $existing = $db->fetchOne(
            'SELECT user_id FROM users WHERE username=:username',
            [
                ':username' => $username
            ]
        );
        if ($existing !== null) {
            return $this->json(['error' => 'Username already exists'], 400);
        }

        // Add the new user
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $db->execute(
            'INSERT INTO users (username, password_hash, date_registered, last_login)
             VALUES (:username, :password_hash, NOW(), NOW())',
            [
                ':username' => $username,
                ':password_hash' => $passwordHash
            ]
        );

        $user = $db->fetchOne(
            'SELECT user_id, username, role
             FROM users WHERE username=:username',
            [
                ':username' => $username
            ]
        );

        return $this->json(
            [
                'message' => 'User registered successfully',
                'user' => $user,
            ],
            201
        );
    }
}
