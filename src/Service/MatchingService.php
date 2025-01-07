<?php

namespace App\Service;

use App\Entity\DeveloperProfile;
use App\Entity\JobPost;
use App\Repository\DeveloperProfileRepository;
use App\Repository\JobPostRepository;

class MatchingService
{
    private $developerRepository;
    private $jobRepository;

    public function __construct(
        DeveloperProfileRepository $developerRepository,
        JobPostRepository $jobRepository
    ) {
        $this->developerRepository = $developerRepository;
        $this->jobRepository = $jobRepository;
    }

    public function findMatchesForJob(JobPost $job): array
    {
        $allDevelopers = $this->developerRepository->findAll();
        $matches = [];

        foreach ($allDevelopers as $developer) {
            $score = $this->calculateMatchScore($job, $developer);
            if ($score > 0) {
                $matches[] = [
                    'developer' => $developer,
                    'score' => $score
                ];
            }
        }

        // Trier par score décroissant
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $matches;
    }

    public function findMatchesForDeveloper(DeveloperProfile $developer): array
    {
        $allJobs = $this->jobRepository->findAll();
        $matches = [];

        foreach ($allJobs as $job) {
            $score = $this->calculateMatchScore($job, $developer);
            if ($score > 0) {
                $matches[] = [
                    'job' => $job,
                    'score' => $score
                ];
            }
        }

        // Trier par score décroissant
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $matches;
    }

    public function calculateMatchScore(JobPost $job, DeveloperProfile $developer): int
    {
        $score = 0;

        // Correspondance des langages de programmation
        $commonLanguages = array_intersect($job->getProgrammingLanguages(), $developer->getProgrammingLanguages());
        $score += count($commonLanguages) * 20;

        // Correspondance du type de contrat
        if ($developer->getPreferredContractType() === $job->getContractType()) {
            $score += 20;
        }

        // Correspondance de la localisation
        if ($developer->getLocation() === $job->getLocation()) {
            $score += 20;
        }

        // Correspondance du niveau d'expérience
        if ($job->getExperienceLevel() <= $developer->getExperienceLevel()) {
            $score += 20;
        }

        // Correspondance du salaire
        if ($developer->getMinimumSalary() !== null && $job->getSalary() !== null) {
            if ($job->getSalary() >= $developer->getMinimumSalary()) {
                $score += 20;
            }
        }

        return $score;
    }
}
