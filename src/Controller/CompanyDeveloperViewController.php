<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Developer;
use App\Entity\DeveloperRating;
use App\Entity\DeveloperProfile;
use App\Entity\JobMatch;
use App\Entity\JobPost;
use App\Entity\ProfileView;
use App\Repository\ProfileViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/developer')]
#[IsGranted('ROLE_COMPANY')]
class CompanyDeveloperViewController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProfileViewRepository $profileViewRepository
    ) {
    }

    #[Route('/{jobId}/{developerId}', name: 'app_company_view_developer')]
    public function viewDeveloperProfileFromJob(int $jobId, int $developerId): Response
    {
        $developer = $this->entityManager->getRepository(DeveloperProfile::class)->find($developerId);

        if (!$developer) {
            throw $this->createNotFoundException('Profil non trouvé.');
        }

        // Si jobId est 201, c'est une vue depuis la recherche
        if ($jobId === 201) {
            // Récupérer ou créer l'offre d'emploi factice pour la recherche
            $job = $this->entityManager->getRepository(JobPost::class)->find(201);
            if (!$job) {
                $job = new JobPost();
                $job->setTitle('Recherche de développeurs');
                $job->setDescription('Offre factice pour la recherche de développeurs');
                $job->setLocation('Global');
                $job->setCompany($this->getUser()->getCompanyProfile());
                $job->setSalaryMin(0);
                $job->setSalaryMax(0);
                $job->setContractType('CDI');
                $job->setCreatedAt(new \DateTimeImmutable());
                $job->setId(201);
                
                $this->entityManager->persist($job);
                $this->entityManager->flush();
            }
        } else {
            $job = $this->entityManager->getRepository(JobPost::class)->find($jobId);
            if (!$job) {
                throw $this->createNotFoundException('Offre non trouvée.');
            }

            // Vérifier que l'entreprise est propriétaire de l'offre
            if ($job->getCompany() !== $this->getUser()->getCompanyProfile()) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette offre.');
            }
        }

        // Incrémente le compteur de vues
        $view = new ProfileView();
        $view->setDeveloper($developer);
        $view->setCompany($this->getUser()->getCompanyProfile());
        $view->setViewedAt(new \DateTimeImmutable());
        $view->setJobPost($job);
        
        $this->entityManager->persist($view);
        $this->entityManager->flush();

        // Calcul de la note moyenne
        $averageRating = $this->entityManager->getRepository(DeveloperRating::class)
            ->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.ratedDeveloper = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Récupérer les statistiques
        $totalViews = $this->profileViewRepository->getViewCount($developer);
        $uniqueCompanyViews = $this->profileViewRepository->getUniqueCompanyViewCount($developer);

        return $this->render('company/developer_profile.html.twig', [
            'developer' => $developer,
            'job' => $job,
            'totalViews' => $totalViews,
            'uniqueCompanyViews' => $uniqueCompanyViews,
            'averageRating' => $averageRating
        ]);
    }

    #[Route('/view/{developerId}', name: 'app_developer_profile_show')]
    public function viewDeveloperProfile(int $developerId): Response
    {
        $developer = $this->entityManager->getRepository(DeveloperProfile::class)->find($developerId);

        if (!$developer) {
            throw $this->createNotFoundException('Profil non trouvé.');
        }

        // Calcul de la note moyenne
        $averageRating = $this->entityManager->getRepository(DeveloperRating::class)
            ->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.ratedDeveloper = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Récupérer les statistiques
        $totalViews = $this->profileViewRepository->getViewCount($developer);
        $uniqueCompanyViews = $this->profileViewRepository->getUniqueCompanyViewCount($developer);

        return $this->render('company/developer_profile.html.twig', [
            'developer' => $developer,
            'totalViews' => $totalViews,
            'uniqueCompanyViews' => $uniqueCompanyViews,
            'averageRating' => $averageRating
        ]);
    }

    #[Route('/search/{id}', name: 'app_company_view_developer_search')]
    public function viewDeveloperProfileFromSearch(DeveloperProfile $developer): Response
    {
        // Incrémente le compteur de vues
        $view = new ProfileView();
        $view->setDeveloper($developer);
        $view->setCompany($this->getUser()->getCompanyProfile());
        $view->setViewedAt(new \DateTimeImmutable());
        
        $this->entityManager->persist($view);
        $this->entityManager->flush();

        // Calcul de la note moyenne
        $averageRating = $this->entityManager->getRepository(DeveloperRating::class)
            ->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.ratedDeveloper = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Récupérer les statistiques
        $totalViews = $this->profileViewRepository->getViewCount($developer);
        $uniqueCompanyViews = $this->profileViewRepository->getUniqueCompanyViewCount($developer);

        return $this->render('company/developer_profile.html.twig', [
            'developer' => $developer,
            'totalViews' => $totalViews,
            'uniqueCompanyViews' => $uniqueCompanyViews,
            'averageRating' => $averageRating
        ]);
    }

    #[Route('/top-viewed', name: 'app_top_viewed_developers')]
    public function topViewedDevelopers(): Response
    {
        $topDevelopers = $this->profileViewRepository->getTopViewedDevelopers();

        return $this->render('company/top_developers.html.twig', [
            'topDevelopers' => $topDevelopers
        ]);
    }
}
