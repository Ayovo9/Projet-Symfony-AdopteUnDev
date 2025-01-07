<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

class FilterService
{
    public function applyJobFilters(QueryBuilder $qb, array $filters): void
    {
        if (!empty($filters['location'])) {
            $qb->andWhere('j.location LIKE :location')
               ->setParameter('location', '%' . $filters['location'] . '%');
        }

        if (!empty($filters['contractType'])) {
            $qb->andWhere('j.contractType = :contractType')
               ->setParameter('contractType', $filters['contractType']);
        }

        if (!empty($filters['minSalary'])) {
            $qb->andWhere('j.salaryMin >= :minSalary')
               ->setParameter('minSalary', $filters['minSalary']);
        }

        if (!empty($filters['programmingLanguages'])) {
            $qb->andWhere('j.programmingLanguages LIKE :languages')
               ->setParameter('languages', '%' . implode('%', $filters['programmingLanguages']) . '%');
        }

        if (!empty($filters['experienceLevel'])) {
            $qb->andWhere('j.experienceLevel = :experienceLevel')
               ->setParameter('experienceLevel', $filters['experienceLevel']);
        }
    }

    public function applyDeveloperFilters(QueryBuilder $qb, array $filters): void
    {
        if (!empty($filters['name'])) {
            $qb->andWhere('d.firstName LIKE :name OR d.lastName LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['location'])) {
            $qb->andWhere('d.location LIKE :location')
               ->setParameter('location', '%' . $filters['location'] . '%');
        }
    }
}
