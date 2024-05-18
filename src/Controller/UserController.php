<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/users', name: 'protected_users_')]
class UserController extends AbstractController
{
    #[Route('', name: 'list', methods: ['get'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAllWithoutPassword();

        return $this->json([
            "status" => true,
            "payload" => [
                "users" => $users
            ]
        ]);
    }
}
