<?php

namespace App\Controller;

use App\Entity\Version;
use App\Repository\LogRepository;
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
                "releaseDate" => $version->getReleaseDate()?->format('Y-m-d'),
                "createdAt" => $version->getCreatedAt()->format('Y-m-d')
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
                "releasedDate" => $version->getReleaseDate()?->format('Y-m-d'),
                "createdAt" => $version->getCreatedAt()->format('Y-m-d')
            ]
        ]);
    }

    #[Route('/{id}', name: 'details', methods: ['GET'])]
    public function details(Version $version, LogRepository $logRepository): JsonResponse
    {
        $versionId = $version->getId();

        return $this->json([
            "status" => true,
            "payload" => [
                "id" => $versionId,
                "name" => $version->getName(),
                "releasedDate" => $version->getReleaseDate()?->format('Y-m-d'),
                "createdAt" => $version->getCreatedAt()->format('Y-m-d'),
                'logs' => $logRepository->findLogsByVersionGroupedByTypeWithDetails($versionId)
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
                "releasedDate" => $version->getReleaseDate()?->format('Y-m-d'),
                "createdAt" => $version->getCreatedAt()->format('Y-m-d')
            ]
        ]);
    }

    #[Route('/{id}', name: 'delete_version', methods: ['DELETE'])]
    public function deleteVersion(VersionRepository $versionRepository, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $version = $versionRepository->find($id);
        if (!is_null($version)) {
            $entityManager->remove($version);
            $entityManager->flush();

            return $this->json([
                "status" => true,
                "message" => "Version successfully deleted."
            ]);
        }

        return $this->json([
            "status" => false,
            "message" => "The version you're trying to delete doesn't exist."
        ]);
    }

    #[Route('/{id}/release', name: 'release', methods: ['PATCH'])]
    public function releaseVersion(VersionRepository $versionRepository, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $version = $versionRepository->find($id);

        if (is_null($version)) {
            return $this->json([
                "status" => false,
                "message" => "The version you're trying to release doesn't exist."
            ]);
        }

        // Check if version has any logs
        if (count($version->getLogs()) == 0) {
            return $this->json([
                "status" => false,
                "message" => "You can't release a version without logs."
            ]);
        }

        $version->setReleaseDate(new \DateTimeImmutable());

        $entityManager->persist($version);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "Version released successfully",
            "payload" => [
                "id" => $version->getId(),
                "name" => $version->getName(),
                "releasedDate" => $version->getReleaseDate()?->format('Y-m-d'),
                "createdAt" => $version->getCreatedAt()->format('Y-m-d')
            ]
        ]);
    }
}
