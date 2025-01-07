<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Entity\JobMatch;
use App\Entity\DeveloperRating;
use App\Form\JobFilterType;
use App\Service\FilterService;
use App\Service\MatchingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/developer')]
class DeveloperDashboardController extends AbstractController
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

    #[Route('/dashboard', name: 'app_developer_dashboard')]
    public function index(Request $request): Response
    {
        $developer = $this->getUser()->getDeveloperProfile();
        
        // Calcul de la note moyenne
        $averageRating = $this->entityManager->getRepository(DeveloperRating::class)
            ->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.ratedDeveloper = :developer')
            ->setParameter('developer', $developer)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
        
        // Création du formulaire de filtre
        $filterForm = $this->createForm(JobFilterType::class);
        $filterForm->handleRequest($request);

        // Récupération des dernières offres et des offres populaires
        $latestJobs = $this->entityManager->getRepository(JobPost::class)->findLatestJobs(3);
        $popularJobs = $this->entityManager->getRepository(JobPost::class)->findMostPopularJobs(3);

        // Récupération des matchs
        $qb = $this->entityManager->getRepository(JobPost::class)->createQueryBuilder('j');
        
        // Application des filtres si le formulaire est soumis
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $this->filterService->applyJobFilters($qb, $filterForm->getData());
        }

        $jobs = $qb->getQuery()->getResult();
        $matches = [];
        $unviewedMatches = [];

        foreach ($jobs as $job) {
            $score = $this->matchingService->calculateMatchScore($job, $developer);
            if ($score > 0) {
                $match = new JobMatch();
                $match->setJobPost($job);
                $match->setDeveloper($developer);
                $match->setMatchScore((string)$score);
                $matches[] = $match;

                // Vérifie si le match a déjà été vu
                $existingMatch = $this->entityManager->getRepository(JobMatch::class)->findOneBy([
                    'jobPost' => $job,
                    'developer' => $developer
                ]);

                if (!$existingMatch || !$existingMatch->isViewed()) {
                    $unviewedMatches[] = $match;
                }
            }
        }

        // Trier les matchs par score décroissant
        usort($matches, function($a, $b) {
            return $b->getMatchScore() - $a->getMatchScore();
        });

        return $this->render('developer/dashboard.html.twig', [
            'filterForm' => $filterForm->createView(),
            'matches' => $matches,
            'unviewedMatches' => $unviewedMatches,
            'latestJobs' => $latestJobs,
            'popularJobs' => $popularJobs,
            'averageRating' => $averageRating
        ]);
    }
}
