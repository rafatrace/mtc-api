<?php

namespace App\Repository;

use App\Entity\Version;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Version>
 *
 * @method Version|null find($id, $lockMode = null, $lockVersion = null)
 * @method Version|null findOneBy(array $criteria, array $orderBy = null)
 * @method Version[]    findAll()
 * @method Version[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Version::class);
    }

    public function findAllReleased(): array
    {
        return $this->createQueryBuilder('version')
            ->andWhere('version.release_date IS NOT NULL')
            ->orderBy('version.release_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllAndOrderByLatest(): array
    {
        return $this->createQueryBuilder('version')
            ->orderBy('version.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
