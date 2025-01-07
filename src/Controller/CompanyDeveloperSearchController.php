<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\DeveloperFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company')]
#[IsGranted('ROLE_COMPANY')]
class CompanyDeveloperSearchController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/developers/search', name: 'app_company_developers_search')]
    public function search(Request $request): Response
    {
        $filterForm = $this->createForm(DeveloperFilterType::class);
        $filterForm->handleRequest($request);

        $qb = $this->entityManager->getRepository(DeveloperProfile::class)
            ->createQueryBuilder('d')
            ->leftJoin('d.user', 'u')
            ->orderBy('d.createdAt', 'DESC');

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData();

            if (!empty($filters['name'])) {
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('LOWER(d.firstName)', ':name'),
                    $qb->expr()->like('LOWER(d.lastName)', ':name')
                ))
                ->setParameter('name', '%' . strtolower($filters['name']) . '%');
            }

            if (!empty($filters['location'])) {
                $qb->andWhere('d.location LIKE :location')
                    ->setParameter('location', '%' . $filters['location'] . '%');
            }
        }

        $developers = $qb->getQuery()->getResult();

        return $this->render('company/developer_search.html.twig', [
            'developers' => $developers,
            'filterForm' => $filterForm->createView()
        ]);
    }
}
