<?php

namespace App\Controller;

use App\Entity\CompanyProfile;
use App\Entity\JobPost;
use App\Form\CompanyProfileImageType;
use App\Form\CompanyProfileType;
use App\Form\JobPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class CompanyProfileController extends AbstractController
{
    #[Route('/company/profile', name: 'app_company_profile')]
    #[IsGranted('ROLE_COMPANY')]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $imageForm = $this->createForm(CompanyProfileImageType::class, $user->getCompanyProfile());

        return $this->render('company/profile.html.twig', [
            'user' => $user,
            'form' => $imageForm->createView(),
        ]);
    }

    #[Route('/company/profile/upload-image', name: 'app_company_upload_image', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function uploadImage(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $companyProfile = $user->getCompanyProfile();
        $form = $this->createForm(CompanyProfileImageType::class, $companyProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();

            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );

                    $oldLogo = $companyProfile->getLogo();
                    if ($oldLogo) {
                        $oldLogoPath = $this->getParameter('profile_images_directory').'/'.$oldLogo;
                        if (file_exists($oldLogoPath)) {
                            unlink($oldLogoPath);
                        }
                    }

                    $companyProfile->setLogo($newFilename);
                    $entityManager->flush();

                    $this->addFlash('success', 'Le logo a été mis à jour avec succès.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du logo.');
                }
            }
        }

        return $this->redirectToRoute('app_company_profile');
    }

    #[Route('/company/profile/edit', name: 'app_company_profile_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companyProfile = $user->getCompanyProfile();
        if (!$companyProfile) {
            $companyProfile = new CompanyProfile();
            $companyProfile->setUser($user);
            $user->setCompanyProfile($companyProfile);
        }

        $form = $this->createForm(CompanyProfileType::class, $companyProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($companyProfile);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
                return $this->redirectToRoute('app_company_profile');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de votre profil.');
            }
        }

        return $this->render('company/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/company/jobs', name: 'app_company_jobs')]
    #[IsGranted('ROLE_COMPANY')]
    public function jobs(): Response
    {
        $user = $this->getUser();
        $companyProfile = $user->getCompanyProfile();
        if (!$companyProfile) {
            $this->addFlash('error', 'Veuillez d\'abord compléter votre profil entreprise.');
            return $this->redirectToRoute('app_company_profile_edit');
        }

        return $this->render('company/jobs.html.twig', [
            'jobs' => $companyProfile->getJobPosts(),
        ]);
    }

    #[Route('/company/jobs/new', name: 'app_company_job_new')]
    #[IsGranted('ROLE_COMPANY')]
    public function newJob(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $companyProfile = $user->getCompanyProfile();
        if (!$companyProfile) {
            $this->addFlash('error', 'Veuillez d\'abord compléter votre profil entreprise.');
            return $this->redirectToRoute('app_company_profile_edit');
        }

        $jobPost = new JobPost();
        $jobPost->setCompany($companyProfile);
        $jobPost->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(JobPostType::class, $jobPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($jobPost);
                $entityManager->flush();

                $this->addFlash('success', 'Votre offre d\'emploi a été publiée avec succès.');
                return $this->redirectToRoute('app_company_jobs');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la publication de votre offre.');
            }
        }

        return $this->render('company/job_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/company/jobs/{id}/edit', name: 'app_company_job_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function editJob(Request $request, JobPost $jobPost, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($jobPost->getCompany()->getUser() !== $user) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(JobPostType::class, $jobPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                $this->addFlash('success', 'Votre offre d\'emploi a été mise à jour avec succès.');
                return $this->redirectToRoute('app_company_jobs');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de votre offre.');
            }
        }

        return $this->render('company/job_edit.html.twig', [
            'form' => $form->createView(),
            'job' => $jobPost,
        ]);
    }

    #[Route('/company/jobs/{id}/delete', name: 'app_company_job_delete', methods: ['POST'])]
    #[IsGranted('ROLE_COMPANY')]
    public function deleteJob(Request $request, JobPost $jobPost, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($jobPost->getCompany()->getUser() !== $user) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete'.$jobPost->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($jobPost);
                $entityManager->flush();

                $this->addFlash('success', 'L\'offre d\'emploi a été supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'offre.');
            }
        }

        return $this->redirectToRoute('app_company_jobs');
    }
}
