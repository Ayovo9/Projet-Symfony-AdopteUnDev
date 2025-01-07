<?php

namespace App\Repository;

use App\Entity\DeveloperRating;
use App\Entity\DeveloperProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeveloperRating>
 */
class DeveloperRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeveloperRating::class);
    }

    public function getAverageRating(DeveloperProfile $developer): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.rating) as avgRating')
            ->where('r.ratedDeveloper = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round($result, 1) : 0.0;
    }

    public function hasRated(DeveloperProfile $ratingDeveloper, DeveloperProfile $ratedDeveloper): bool
    {
        $rating = $this->findOneBy([
            'ratingDeveloper' => $ratingDeveloper,
            'ratedDeveloper' => $ratedDeveloper
        ]);

        return $rating !== null;
    }
}
