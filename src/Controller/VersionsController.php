<?php

namespace App\Controller;

use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse};
use Symfony\Component\Routing\Attribute\Route;

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
                "releaseDate" => $version->getReleaseDate()
            ];
        }

        return $this->json([
            "status" => true,
            "payload" => [
                "versions" => $transformedVersions
            ]
        ]);
    }
}
