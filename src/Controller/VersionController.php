<?php

namespace App\Controller;

use App\Entity\Version;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/versions', name: 'api_versions_')]
class VersionController extends AbstractController
{
    #[Route('/public', name: 'public', methods: ['get'])]
    public function index(VersionRepository $versionRepository): JsonResponse
    {
        $versions = $versionRepository
            ->findAll();

        $data = [];

        foreach ($versions as $version) {
            $logs = $version->getLogs();
            dd($logs);

            $data[] = [
                'id' => $version->getId(),
                'name' => $version->getName(),
                'releaseDate' => $version->getReleaseDate(),
                'logs' => $logs
            ];
        }

        return $this->json($data);
    }
}
