<?php

namespace App\Controller;

use App\Entity\Version;
use App\Repository\LogRepository;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/versions', name: 'api_versions_')]
class VersionController extends AbstractController
{
    #[Route('/public', name: 'public', methods: ['get'])]
    public function index(VersionRepository $versionRepository, LogRepository $logRepository): JsonResponse
    {
        $versions = $versionRepository->findAllActiveWithLogs();

        $data = [];

        foreach ($versions as $version) {
            $versionId = $version->getId();

            $data[] = [
                'id' => $versionId,
                'name' => $version->getName(),
                'releaseDate' => $version->getReleaseDate()->format('jS F, Y'),
                'logs' => $logRepository->findLogsByVersionGroupedByType($versionId)
            ];
        }

        return $this->json([
            "status" => true,
            "payload" => [
                "versions" => $data
            ]
        ]);
    }
}
