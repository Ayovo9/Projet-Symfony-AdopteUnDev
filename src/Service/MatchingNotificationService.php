<?php

namespace App\Service;

use App\Entity\DeveloperProfile;
use App\Entity\JobPost;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MatchingNotificationService
{
    public function __construct(
        private NotificationService $notificationService,
        private EntityManagerInterface $entityManager
    ) {}

    public function notifyNewJobMatch(DeveloperProfile $developer, JobPost $jobPost): void
    {
        // Créer une notification pour le développeur
        $this->notificationService->createNotification(
            $developer->getUser(),
            'new_job_match',
            sprintf('Nouvelle offre d\'emploi correspondant à votre profil : %s', $jobPost->getTitle()),
            [
                'jobId' => $jobPost->getId(),
                'companyName' => $jobPost->getCompany()->getName()
            ]
        );
    }

    public function notifyNewDeveloperMatch(JobPost $jobPost, DeveloperProfile $developer): void
    {
        // Créer une notification pour l'entreprise
        $this->notificationService->createNotification(
            $jobPost->getCompany()->getUser(),
            'new_developer_match',
            sprintf('Nouveau développeur correspondant à votre offre "%s" : %s %s',
                $jobPost->getTitle(),
                $developer->getFirstName(),
                $developer->getLastName()
            ),
            [
                'developerId' => $developer->getId(),
                'jobId' => $jobPost->getId()
            ]
        );
    }

    public function checkNewMatchesForDeveloper(DeveloperProfile $developer): void
    {
        $lastLoginDate = $developer->getUser()->getLastLoginAt() ?? new \DateTime('-1 day');

        $newMatchingJobs = $this->entityManager->getRepository(JobPost::class)
            ->findNewMatchingJobs($developer, $lastLoginDate);

        foreach ($newMatchingJobs as $jobPost) {
            $this->notifyNewJobMatch($developer, $jobPost);
        }
    }

    public function checkNewMatchesForJobPost(JobPost $jobPost): void
    {
        $lastCheckedDate = $jobPost->getLastMatchCheckAt() ?? $jobPost->getCreatedAt();

        $newMatchingDevelopers = $this->entityManager->getRepository(DeveloperProfile::class)
            ->findNewMatchingDevelopers($jobPost, $lastCheckedDate);

        foreach ($newMatchingDevelopers as $developer) {
            $this->notifyNewDeveloperMatch($jobPost, $developer);
        }

        $jobPost->setLastMatchCheckAt(new \DateTime());
        $this->entityManager->flush();
    }
}
