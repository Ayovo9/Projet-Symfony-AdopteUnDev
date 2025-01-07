<?php

namespace App\Repository;

use App\Entity\DeveloperProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeveloperProfile>
 *
 * @method DeveloperProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeveloperProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeveloperProfile[]    findAll()
 * @method DeveloperProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeveloperProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeveloperProfile::class);
    }

    public function findMostViewed(int $limit = 3): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->leftJoin('App\Entity\ProfileView', 'v', 'WITH', 'v.developer = d')
            ->groupBy('d.id')
            ->orderBy('COUNT(v.id)', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findLatestCreated(int $limit = 3): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
