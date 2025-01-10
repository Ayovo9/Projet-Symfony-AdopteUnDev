<?php

namespace App\Repository;

use App\Entity\JobPost;
use App\Entity\DeveloperProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobPost>
 *
 * @method JobPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPost[]    findAll()
 * @method JobPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPost::class);
    }

    /**
     * Trouve les dernières offres d'emploi publiées
     */
    public function findLatestJobs(int $limit = 3): array
    {
        return $this->createQueryBuilder('j')
            ->orderBy('j.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les offres d'emploi avec le plus de matchs
     */
    public function findMostPopularJobs(int $limit = 3): array
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.matches', 'm')
            ->groupBy('j.id')
            ->orderBy('COUNT(m.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les offres d'emploi en fonction des critères de recherche
     */
    public function findBySearchCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.company', 'c')
            ->addSelect('c');

        if (!empty($criteria['search'])) {
            $qb->andWhere('j.title LIKE :search OR j.programmingLanguages LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        if (!empty($criteria['location'])) {
            $qb->andWhere('j.location LIKE :location')
               ->setParameter('location', '%' . $criteria['location'] . '%');
        }

        if (!empty($criteria['contractType'])) {
            $qb->andWhere('j.contractType = :contractType')
               ->setParameter('contractType', $criteria['contractType']);
        }

        return $qb
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les offres d'emploi correspondant au profil du développeur
     */
    public function findNewMatchingJobs(DeveloperProfile $developer, \DateTime $since): array
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.createdAt >= :since')
            ->setParameter('since', $since);

        // Ajouter les critères de correspondance basés sur les compétences
        if (!empty($developer->getProgrammingLanguages())) {
            $orConditions = [];
            foreach ($developer->getProgrammingLanguages() as $key => $skill) {
                $orConditions[] = "j.programmingLanguages LIKE :skill" . $key;
                $qb->setParameter('skill' . $key, '%' . $skill . '%');
            }
            
            if (!empty($orConditions)) {
                $qb->andWhere('(' . implode(' OR ', $orConditions) . ')');
            }
        }

        return $qb->getQuery()->getResult();
    }
}
