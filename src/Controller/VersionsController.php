<?php

namespace App\Controller;

use App\Entity\Version;
use App\Repository\VersionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/v1/versions', name: 'protected_versions_')]
class VersionsController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function index(VersionRepository $versionRepository): JsonResponse
    {
        $versions = $versionRepository->findAllAndOrderByLatest();

        $transformedVersions = [];
        foreach ($versions as $version) {
            $transformedVersions[] = [
                "id" => $version->getId(),
                "name" => $version->getName(),
                "releaseDate" => $version->getReleaseDate(),
                "createdAt" => $version->getCreatedAt(),
            ];
        }

        return $this->json([
            "status" => true,
            "payload" => [
                "versions" => $transformedVersions
            ]
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function register(EntityManagerInterface $entityManager): JsonResponse
    {
        $version = new Version();
        $version->setName('?.?.?');
        $version->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($version);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "Version created successfully",
            "payload" => [
                "id" => $version->getId(),
                "name" => $version->getName(),
                "releasedDate" => $version->getReleaseDate(),
                "createdAt" => $version->getCreatedAt()
            ]
        ]);
    }

    #[Route('/{id}', name: 'update_name', methods: ['PATCH'])]
    public function updateName(VersionRepository $versionRepository, EntityManagerInterface $entityManager, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if name field is received
        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json([
                "status" => false,
                "message" => "Please you need to set a new name."
            ]);
        }

        $version = $versionRepository->find($id);
        $version->setName($data['name']);

        $entityManager->persist($version);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "Version created successfully",
            "payload" => [
                "id" => $version->getId(),
                "name" => $version->getName(),
                "releasedDate" => $version->getReleaseDate(),
                "createdAt" => $version->getCreatedAt()
            ]
        ]);
    }
}
