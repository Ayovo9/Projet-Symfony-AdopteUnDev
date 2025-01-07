<?php

namespace App\Repository;

use App\Entity\JobMatch;
use App\Entity\DeveloperProfile;
use App\Entity\JobPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobMatch>
 *
 * @method JobMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobMatch[]    findAll()
 * @method JobMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobMatch::class);
    }

    /**
     * Trouve les meilleurs matches pour un développeur
     */
    public function findBestMatchesForDeveloper(DeveloperProfile $developer, int $limit = 10): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.developer = :developer')
            ->setParameter('developer', $developer)
            ->orderBy('m.matchScore', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les meilleurs matches pour une offre d'emploi
     */
    public function findBestMatchesForJobPost(JobPost $jobPost, int $limit = 10): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.jobPost = :jobPost')
            ->setParameter('jobPost', $jobPost)
            ->orderBy('m.matchScore', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les matches non vus pour un développeur
     */
    public function findUnviewedMatchesForDeveloper(DeveloperProfile $developer): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.developer = :developer')
            ->andWhere('m.isViewed = false')
            ->setParameter('developer', $developer)
            ->orderBy('m.matchScore', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les matches sauvegardés pour un développeur
     */
    public function findSavedMatchesForDeveloper(DeveloperProfile $developer): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.developer = :developer')
            ->andWhere('m.isSaved = true')
            ->setParameter('developer', $developer)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
