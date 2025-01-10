<?php

namespace App\Controller;

use App\Form\JobSearchType;
use App\Repository\JobPostRepository;
use App\Service\SearchHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobs')]
class JobBrowseController extends AbstractController
{
    public function __construct(
        private SearchHistoryService $searchHistoryService
    ) {
    }

    #[Route('/', name: 'app_jobs_browse')]
    public function index(Request $request, JobPostRepository $jobRepository): Response
    {
        $form = $this->createForm(JobSearchType::class);
        $form->handleRequest($request);

        $jobs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $jobs = $jobRepository->findBySearchCriteria($searchData);
            
            // Log the search
            if ($this->getUser()) {
                $query = $searchData['query'] ?? '';
                unset($searchData['query']);
                $this->searchHistoryService->logSearch(
                    $this->getUser(),
                    $query,
                    $searchData
                );
            }
        } else {
            $jobs = $jobRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('job/browse.html.twig', [
            'jobs' => $jobs,
            'form' => $form->createView(),
        ]);
    }
}
