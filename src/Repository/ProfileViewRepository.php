<?php

namespace App\Repository;

use App\Entity\ProfileView;
use App\Entity\DeveloperProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfileView>
 */
class ProfileViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileView::class);
    }

    public function getViewCount(DeveloperProfile $developer): int
    {
        return $this->createQueryBuilder('pv')
            ->select('COUNT(pv.id)')
            ->where('pv.developer = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getUniqueCompanyViewCount(DeveloperProfile $developer): int
    {
        return $this->createQueryBuilder('pv')
            ->select('COUNT(DISTINCT pv.company)')
            ->where('pv.developer = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopViewedDevelopers(int $limit = 10): array
    {
        return $this->createQueryBuilder('pv')
            ->select('d as developer, COUNT(pv.id) as viewCount')
            ->join('pv.developer', 'd')
            ->groupBy('d.id')
            ->orderBy('viewCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
