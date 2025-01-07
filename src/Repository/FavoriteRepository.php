<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findUserFavorites(User $user)
    {
        return $this->createQueryBuilder('f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function isInFavorites(User $user, $item): bool
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.user = :user')
            ->setParameter('user', $user);

        if ($item instanceof \App\Entity\DeveloperProfile) {
            $qb->andWhere('f.developer = :item');
        } elseif ($item instanceof \App\Entity\JobPost) {
            $qb->andWhere('f.jobPost = :item');
        }

        $qb->setParameter('item', $item);

        return count($qb->getQuery()->getResult()) > 0;
    }
}
