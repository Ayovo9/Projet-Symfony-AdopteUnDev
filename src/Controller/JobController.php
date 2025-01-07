<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Form\JobPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/job')]
class JobController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        // Si l'utilisateur est une entreprise, il ne peut voir que ses propres offres
        if ($this->isGranted('ROLE_COMPANY') && $job->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir cette offre.');
        }

        return $this->render('job/show.html.twig', [
            'job' => $job,
            'isOwner' => $this->isGranted('ROLE_COMPANY') && $job->getCompany() === $this->getUser()->getCompanyProfile()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_job_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JobPost $job): Response
    {
        // Vérifier que l'entreprise est propriétaire de l'offre
        if ($job->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette offre.');
        }

        $form = $this->createForm(JobPostType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre offre a été mise à jour avec succès !');
            return $this->redirectToRoute('app_company_dashboard');
        }

        return $this->render('job/edit.html.twig', [
            'form' => $form->createView(),
            'job' => $job
        ]);
    }

    #[Route('/{id}/delete', name: 'app_job_delete', methods: ['POST'])]
    public function delete(Request $request, JobPost $job): Response
    {
        // Vérifier que l'entreprise est propriétaire de l'offre
        if ($job->getCompany() !== $this->getUser()->getCompanyProfile()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette offre.');
        }

        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($job);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'offre a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_company_dashboard');
    }
}
