<?php

namespace App\Repository;

use App\Entity\SearchHistory;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchHistory>
 */
class SearchHistoryRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchHistory::class);
        $this->entityManager = $registry->getManager();
    }

    public function findUserHistory(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('sh')
            ->andWhere('sh.user = :user')
            ->setParameter('user', $user)
            ->orderBy('sh.searchedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function save(SearchHistory $searchHistory): void
    {
        $this->entityManager->persist($searchHistory);
        $this->entityManager->flush();
    }
}
