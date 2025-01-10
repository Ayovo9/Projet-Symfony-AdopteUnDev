<?php

namespace App\Controller;

use App\Entity\JobApplication;
use App\Entity\JobPost;
use App\Form\JobApplicationType;
use App\Repository\JobApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/application')]
class JobApplicationController extends AbstractController
{
    #[Route('/apply/{id}', name: 'app_job_application_new')]
    #[IsGranted('ROLE_DEVELOPER')]
    public function apply(Request $request, JobPost $jobPost, EntityManagerInterface $entityManager): Response
    {
        $application = new JobApplication();
        $application->setJobPost($jobPost);
        $application->setDeveloper($this->getUser()->getDeveloperProfile());

        $form = $this->createForm(JobApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($application);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute('app_job_show', ['id' => $jobPost->getId()]);
        }

        return $this->render('job_application/new.html.twig', [
            'form' => $form->createView(),
            'jobPost' => $jobPost,
        ]);
    }

    #[Route('/my-applications', name: 'app_my_applications')]
    #[IsGranted('ROLE_DEVELOPER')]
    public function myApplications(JobApplicationRepository $repository): Response
    {
        $applications = $repository->findByDeveloper($this->getUser()->getDeveloperProfile());

        return $this->render('job_application/my_applications.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/company/applications', name: 'app_company_applications')]
    #[IsGranted('ROLE_COMPANY')]
    public function companyApplications(JobApplicationRepository $repository): Response
    {
        $applications = $repository->createQueryBuilder('a')
            ->join('a.jobPost', 'j')
            ->where('j.company = :company')
            ->setParameter('company', $this->getUser()->getCompanyProfile())
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('job_application/company_applications.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/{id}/update-status', name: 'app_application_update_status', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function updateStatus(Request $request, JobApplication $application, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'application appartient bien à l'entreprise connectée
        if ($application->getJobPost()->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException();
        }

        $status = $request->request->get('status');
        $feedback = $request->request->get('feedback');

        if (in_array($status, ['accepted', 'rejected'])) {
            $application->setStatus($status);
            $application->setFeedback($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Le statut de la candidature a été mis à jour.');
        }

        return $this->redirectToRoute('app_company_applications');
    }
}
