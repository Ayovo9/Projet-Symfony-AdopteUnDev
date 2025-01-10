<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Form\JobPostType;
use App\Service\MatchingNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/job')]
class JobController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MatchingNotificationService $matchingNotificationService
    ) {
    }

    #[Route('/new', name: 'app_company_job_new')]
    #[IsGranted('ROLE_COMPANY')]
    public function new(Request $request): Response
    {
        $job = new JobPost();
        $job->setCompany($this->getUser()->getCompanyProfile());
        $job->setCreatedAt(new \DateTime());

        $form = $this->createForm(JobPostType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($job);
            $this->entityManager->flush();

            // Chercher les développeurs qui correspondent et les notifier
            $this->matchingNotificationService->checkNewMatchesForJobPost($job);

            $this->addFlash('success', 'Votre offre a été publiée avec succès !');
            return $this->redirectToRoute('app_company_dashboard');
        }

        return $this->render('job/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_job_show', methods: ['GET'])]
    public function show(JobPost $job): Response
    {
        // Si l'utilisateur est un développeur, il peut voir toutes les offres
        if ($this->isGranted('ROLE_DEVELOPER')) {
            return $this->render('job/show.html.twig', [
                'job' => $job
            ]);
        }

        // Si l'utilisateur est une entreprise, il ne peut voir que ses propres offres
        if ($this->isGranted('ROLE_COMPANY') && $job->getCompany() === $this->getUser()->getCompanyProfile()) {
            return $this->render('job/show.html.twig', [
                'job' => $job
            ]);
        }

        throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette offre.');
    }

    #[Route('/{id}/edit', name: 'app_company_job_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function edit(Request $request, JobPost $job): Response
    {
        if ($job->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette offre.');
        }

        $form = $this->createForm(JobPostType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            // Vérifier les nouveaux matchs après la modification
            $this->matchingNotificationService->checkNewMatchesForJobPost($job);

            $this->addFlash('success', 'Votre offre a été mise à jour avec succès !');
            return $this->redirectToRoute('app_company_dashboard');
        }

        return $this->render('job/edit.html.twig', [
            'form' => $form->createView(),
            'job' => $job
        ]);
    }

    #[Route('/{id}/delete', name: 'app_company_job_delete', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function delete(Request $request, JobPost $job): Response
    {
        if ($job->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette offre.');
        }

        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'offre a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_company_dashboard');
    }
}
