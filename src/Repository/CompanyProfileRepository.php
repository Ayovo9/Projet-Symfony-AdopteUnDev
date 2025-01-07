<?php

namespace App\Repository;

use App\Entity\CompanyProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyProfile>
 *
 * @method CompanyProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyProfile[]    findAll()
 * @method CompanyProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyProfile::class);
    }
}
