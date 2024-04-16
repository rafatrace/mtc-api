<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function findLogsByVersionGroupedByType(int $version): array
    {
        // Get all logs
        $logs = $this->createQueryBuilder('log')
            ->andWhere('log.version = :versionId')
            ->setParameter('versionId', $version)
            ->getQuery()
            ->getResult();

        // Group by type
        $groupedLogs = [
            "new" => [],
            "improved" => [],
            "fixed" => [],
            "remove" => [],
        ];

        foreach ($logs as $log) {
            $groupedLogs[$log->getType()][] = $log->getText();
        }

        return $groupedLogs;
    }
}
