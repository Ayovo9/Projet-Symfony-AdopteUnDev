<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Form\JobSearchType;
use App\Repository\JobPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobs')]
class JobBrowseController extends AbstractController
{
    #[Route('/', name: 'app_jobs_browse')]
    public function index(Request $request, JobPostRepository $jobRepository): Response
    {
        $form = $this->createForm(JobSearchType::class);
        $form->handleRequest($request);

        $jobs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $jobs = $jobRepository->findBySearchCriteria($searchData);
        } else {
            $jobs = $jobRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('job/browse.html.twig', [
            'jobs' => $jobs,
            'form' => $form->createView(),
        ]);
    }
}
