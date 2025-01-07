<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Entity\DeveloperProfile;
use App\Form\JobFilterType;
use App\Service\FilterService;
use App\Service\MatchingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company')]
#[IsGranted('ROLE_COMPANY')]
class CompanyDashboardController extends AbstractController
{
    private $entityManager;
    private $matchingService;
    private $filterService;

    public function __construct(
        EntityManagerInterface $entityManager,
        MatchingService $matchingService,
        FilterService $filterService
    ) {
        $this->entityManager = $entityManager;
        $this->matchingService = $matchingService;
        $this->filterService = $filterService;
    }

    #[Route('/dashboard', name: 'app_company_dashboard')]
    public function index(Request $request): Response
    {
        $company = $this->getUser()->getCompanyProfile();
        if (!$company) {
            return $this->redirectToRoute('app_company_profile_create');
        }

        // Création du formulaire de filtre
        $filterForm = $this->createForm(JobFilterType::class);
        $filterForm->handleRequest($request);

        // Récupération des offres d'emploi de l'entreprise
        $jobsWithMatches = [];
        $qb = $this->entityManager->getRepository(JobPost::class)
            ->createQueryBuilder('j')
            ->where('j.company = :company')
            ->setParameter('company', $company)
            ->orderBy('j.createdAt', 'DESC');

        // Application des filtres si le formulaire est soumis
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $this->filterService->applyJobFilters($qb, $filterForm->getData());
        }

        $jobs = $qb->getQuery()->getResult();

        // Pour chaque offre, récupérer les développeurs correspondants
        foreach ($jobs as $job) {
            // Création de la requête pour les développeurs
            $qb = $this->entityManager->getRepository(DeveloperProfile::class)
                ->createQueryBuilder('d');
            
            // Application des filtres si le formulaire est soumis
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $this->filterService->applyDeveloperFilters($qb, $filterForm->getData());
            }

            $developers = $qb->getQuery()->getResult();
            
            // Calcul des scores de matching
            $matches = [];
            foreach ($developers as $developer) {
                $score = $this->matchingService->calculateMatchScore($job, $developer);
                if ($score > 0) {
                    $matches[] = [
                        'developer' => $developer,
                        'score' => $score
                    ];
                }
            }

            // Tri des matchs par score décroissant
            usort($matches, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            $jobsWithMatches[] = [
                'job' => $job,
                'matches' => $matches
            ];
        }

        // Récupération des profils les plus consultés et derniers créés
        $developerRepository = $this->entityManager->getRepository(DeveloperProfile::class);
        $mostViewedDevelopers = $developerRepository->findMostViewed(3);
        $latestDevelopers = $developerRepository->findLatestCreated(3);

        return $this->render('company/dashboard.html.twig', [
            'jobsWithMatches' => $jobsWithMatches,
            'filterForm' => $filterForm->createView(),
            'company' => $company,
            'mostViewedDevelopers' => $mostViewedDevelopers,
            'latestDevelopers' => $latestDevelopers
        ]);
    }

    #[Route('/job/{id}/matches', name: 'app_job_matches')]
    #[IsGranted('ROLE_COMPANY', message: 'Vous n\'êtes pas autorisé à voir ces matchs.')]
    public function viewJobMatches(JobPost $job): Response
    {
        $company = $this->getUser()->getCompanyProfile();
        
        // Vérifier que l'entreprise est propriétaire de l'offre
        if ($job->getCompany()->getId() !== $company->getId()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir ces matchs.');
        }

        // Récupérer les développeurs qui matchent
        $matches = [];
        $developers = $this->entityManager->getRepository(DeveloperProfile::class)->findAll();
        
        foreach ($developers as $developer) {
            $score = $this->matchingService->calculateMatchScore($job, $developer);
            if ($score > 0) {
                $matches[] = [
                    'developer' => $developer,
                    'score' => $score
                ];
            }
        }

        // Tri des matchs par score décroissant
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $this->render('matching/job_matches.html.twig', [
            'job' => $job,
            'matches' => $matches
        ]);
    }
}
