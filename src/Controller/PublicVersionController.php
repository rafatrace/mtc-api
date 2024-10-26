<?php

namespace App\Controller;

use App\Entity\Version;
use App\Repository\LogRepository;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/versions', name: 'versions_')]
class PublicVersionController extends AbstractController
{
    #[Route('/public', name: 'public', methods: ['GET'])]
    public function index(VersionRepository $versionRepository, LogRepository $logRepository): JsonResponse
    {
        $versions = $versionRepository->findAllAndOrderByLatest();

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
