<?php

namespace App\Controller;

use App\Entity\Log;
use App\Repository\LogRepository;
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

    #[Route('/{id}', name: 'update_log_text', methods: ['PATCH'])]
    public function updateLogText(LogRepository $logRepository, EntityManagerInterface $entityManager, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if text field is received
        if (!isset($data['text']) || empty($data['text'])) {
            return $this->json([
                "status" => false,
                "message" => "Please you need to set a new text."
            ]);
        }

        $log = $logRepository->find($id);
        $log->setText($data['text']);

        $entityManager->persist($log);
        $entityManager->flush();

        return $this->json([
            "status" => true,
            "message" => "Log text edited successfully"
        ]);
    }

    #[Route('/{id}', name: 'delete_log', methods: ['DELETE'])]
    public function deleteLog(LogRepository $logRepository, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $log = $logRepository->find($id);
        if (!is_null($log)) {
            $entityManager->remove($log);
            $entityManager->flush();

            return $this->json([
                "status" => true,
                "message" => "Log successfully deleted."
            ]);
        }

        return $this->json([
            "status" => false,
            "message" => "The log you're trying to delete doesn't exist."
        ]);
    }
}
