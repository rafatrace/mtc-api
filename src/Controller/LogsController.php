<?php

namespace App\Controller;

use App\Entity\Log;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/v1/logs', name: 'protected_logs_')]
class LogsController extends AbstractController
{
    #[Route('', name: 'add_to_version', methods: ['POST'])]
    public function addToVersion(VersionRepository $versionRepository, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if all field are received
        if (!isset($data['versionId']) || empty($data['versionId'])) {
            return $this->json([
                "status" => false,
                "message" => "Some fields are missing. [versionId, type, text]"
            ]);
        }

        // Check if version exists
        $version = $versionRepository->find($data['versionId']);
        if (is_null($version)) {
            return $this->json([
                "status" => false,
                "message" => "This version doesn't exists"
            ]);
        }

        // Check if type is one of the 4 available types
        $types = ["fixed", "improved", "new", "remove"];
        if (!in_array($data['type'], $types)) {
            return $this->json([
                "status" => false,
                "message" => "The type needs to be one of the following: fixed, improved, new, remove."
            ]);
        }

        $log = new Log();
        $log->setVersion($version);
        $log->setType($data['type']);
        $log->setText($data['text']);

        $entityManager->persist($log);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "Log created successfully",
            "payload" => [
                "id" => $log->getId(),
                "text" => $log->getText(),
                "type" => $log->getType(),
                "versionId" => $log->getVersion()->getId()
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
}
