<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/api/profile', name: 'api_profile', methods: ['GET'])]
    public function getProfile(Database $db): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            $this->json(['error' => 'User not found'], 401);
        }

        $u = $db->fetchOne(
            'SELECT username, user_id, last_login, role FROM users WHERE user_id=:uid',
            [
                ':uid' => $user->getId()
            ]
        );
        if (!$u) {
            $this->json(['error' => 'User does not exist'], 401);
        }
        return $this->json($u);
    }
}
