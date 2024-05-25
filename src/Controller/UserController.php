<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/v1/users', name: 'protected_users_')]
class UserController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
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

    #[Route('', name: 'register', methods: ['POST'])]
    public function register(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if required fields are received
        if (!isset($data['email']) || empty($data['email']) || !isset($data['password']) || empty($data['password']) || !isset($data['name']) || empty($data['name'])) {
            return $this->json([
                "status" => false,
                "message" => "Please you need to set email, password and name for a new user."
            ]);
        }

        // Check if passwords are equal
        if ($data['password'] != $data['confirm_password']) {
            return $this->json([
                "status" => false,
                "message" => "Confirm password is different from password."
            ]);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setAvatar($data['avatar']);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "User created successfully",
            "payload" => [
                "userId" => $user->getId()
            ]
        ]);
    }
}
