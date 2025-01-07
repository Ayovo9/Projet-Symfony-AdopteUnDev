<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\DeveloperProfile;
use App\Entity\CompanyProfile;
use App\Entity\JobPost;
use App\Entity\JobMatch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    private $programmingLanguages = [
        'PHP', 'JavaScript', 'Python', 'Java', 'C++', 'C#', 'Ruby', 
        'Swift', 'Go', 'Rust', 'TypeScript', 'Kotlin', 'Scala'
    ];
    private $locations = [
        'Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Lille', 'Toulouse', 
        'Nantes', 'Strasbourg', 'Montpellier', 'Nice'
    ];
    private $contractTypes = ['CDI', 'CDD', 'Freelance', 'Stage', 'Alternance'];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $developers = [];
        $companies = [];
        $jobs = [];

        // Création des développeurs
        for ($i = 1; $i <= 50; $i++) {
            $devUser = new User();
            $devUser->setEmail("dev{$i}@test.fr");
            $devUser->setUsername("Développeur {$i}");
            $devUser->setRoles(['ROLE_DEVELOPER']);
            $devUser->setPassword($this->passwordHasher->hashPassword($devUser, 'appappapp'));

            $devProfile = new DeveloperProfile();
            $devProfile->setFirstName("Prénom{$i}");
            $devProfile->setLastName("Nom{$i}");
            $devProfile->setLocation($this->locations[array_rand($this->locations)]);
            // Sélection aléatoire de 3 à 6 langages
            $selectedLanguages = array_rand(array_flip($this->programmingLanguages), rand(3, 6));
            $devProfile->setProgrammingLanguages($selectedLanguages);
            $devProfile->setExperienceLevel(rand(0, 15));
            $devProfile->setPreferredContractType($this->contractTypes[array_rand($this->contractTypes)]);
            $devProfile->setMinimumSalary(rand(30, 120));
            $devProfile->setBio("Bio du développeur {$i}");
            
            $devUser->setDeveloperProfile($devProfile);
            $manager->persist($devUser);
            $manager->persist($devProfile);
            $developers[] = $devProfile;
        }

        // Création des entreprises
        for ($i = 1; $i <= 30; $i++) {
            $companyUser = new User();
            $companyUser->setEmail("ent{$i}@test.fr");
            $companyUser->setUsername("Entreprise {$i}");
            $companyUser->setRoles(['ROLE_COMPANY']);
            $companyUser->setPassword($this->passwordHasher->hashPassword($companyUser, 'appappapp'));

            $companyProfile = new CompanyProfile();
            $companyProfile->setName("Entreprise {$i}");
            $companyProfile->setDescription("Description de l'entreprise {$i}");
            $companyProfile->setLocation($this->locations[array_rand($this->locations)]);
            $companyProfile->setWebsite("https://entreprise{$i}.fr");
            
            $companyUser->setCompanyProfile($companyProfile);
            $manager->persist($companyUser);
            $manager->persist($companyProfile);
            $companies[] = $companyProfile;

            // Création des offres pour chaque entreprise
            $numJobs = rand(5, 10); // Entre 5 et 10 offres par entreprise
            for ($j = 1; $j <= $numJobs; $j++) {
                $job = new JobPost();
                $job->setTitle("Poste {$j} - Entreprise {$i}");
                $job->setDescription("Description du poste {$j} de l'entreprise {$i}");
                $job->setLocation($companyProfile->getLocation());
                $job->setCompanyProfile($companyProfile);
                $job->setContractType($this->contractTypes[array_rand($this->contractTypes)]);
                $job->setSalary(rand(30, 120));
                $job->setExperienceLevel(rand(0, 15));
                // Sélection aléatoire de 2 à 5 langages
                $selectedLanguages = array_rand(array_flip($this->programmingLanguages), rand(2, 5));
                $job->setProgrammingLanguages($selectedLanguages);
                
                $manager->persist($job);
                $jobs[] = $job;
            }
        }

        // Création des matches
        foreach ($jobs as $job) {
            foreach ($developers as $developer) {
                // Calcul simple du score de match
                $score = 0;
                
                // Match sur la localisation
                if ($job->getLocation() === $developer->getLocation()) {
                    $score += 30;
                }
                
                // Match sur le type de contrat
                if ($job->getContractType() === $developer->getPreferredContractType()) {
                    $score += 20;
                }
                
                // Match sur le salaire
                if ($job->getSalary() >= $developer->getMinimumSalary()) {
                    $score += 20;
                }
                
                // Match sur l'expérience
                $expDiff = abs($job->getExperienceLevel() - $developer->getExperienceLevel());
                if ($expDiff <= 2) {
                    $score += 15;
                }
                
                // Match sur les langages
                $commonLanguages = array_intersect($job->getProgrammingLanguages(), $developer->getProgrammingLanguages());
                $score += count($commonLanguages) * 5;
                
                // Ne créer le match que si le score est supérieur à 40%
                if ($score >= 40) {
                    $match = new JobMatch();
                    $match->setJob($job);
                    $match->setDeveloper($developer);
                    $match->setScore($score);
                    $match->setStatus('pending');
                    $manager->persist($match);
                }
            }
        }

        $manager->flush();
    }
}
