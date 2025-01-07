<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\JobPost;
use App\Entity\CompanyProfile;
use App\Entity\DeveloperProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:generate-test-data',
    description: 'Génère des données de test pour les développeurs et les entreprises'
)]
class GenerateTestDataCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Liste des langages de programmation
        $languages = ['PHP', 'JavaScript', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Swift', 'Kotlin', 'TypeScript'];
        
        // Liste des villes
        $cities = ['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Lille', 'Toulouse', 'Nantes', 'Strasbourg', 'Nice', 'Rennes'];
        
        // Liste des types de contrat
        $contractTypes = ['CDI', 'CDD', 'Freelance', 'Stage', 'Alternance'];

        // Création des développeurs
        $io->section('Création des développeurs');
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail("dev$i@test.fr");
            $user->setUsername("dev$i");
            $user->setRoles(['ROLE_DEVELOPER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'apppappp'));

            $profile = new DeveloperProfile();
            $profile->setFirstName("Développeur");
            $profile->setLastName("$i");
            $profile->setBio("Je suis un développeur passionné avec une expertise en développement web.");
            $profile->setExperienceLevel(rand(1, 10));
            $profile->setLocation($cities[array_rand($cities)]);
            
            // Attribution aléatoire de 3-5 langages
            $devLanguages = [];
            $numLanguages = rand(3, 5);
            $shuffledLanguages = $languages;
            shuffle($shuffledLanguages);
            for ($j = 0; $j < $numLanguages; $j++) {
                $devLanguages[] = $shuffledLanguages[$j];
            }
            $profile->setProgrammingLanguages($devLanguages);
            
            $profile->setPreferredContractType($contractTypes[array_rand($contractTypes)]);
            $profile->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($profile);
            
            $io->text("Développeur $i créé");
        }

        // Création des entreprises et leurs offres
        $io->section('Création des entreprises et de leurs offres');
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail("ent$i@test.fr");
            $user->setUsername("ent$i");
            $user->setRoles(['ROLE_COMPANY']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'apppappp'));

            $profile = new CompanyProfile();
            $profile->setName("Entreprise $i");
            $profile->setDescription("Nous sommes une entreprise innovante spécialisée dans le développement logiciel.");
            $profile->setLocation($cities[array_rand($cities)]);
            $profile->setWebsite("https://entreprise$i.fr");
            $profile->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($profile);

            // Création de 10 offres pour chaque entreprise
            for ($j = 1; $j <= 10; $j++) {
                $jobPost = new JobPost();
                $jobPost->setTitle("Développeur " . $languages[array_rand($languages)]);
                $jobPost->setDescription("Nous recherchons un développeur talentueux pour rejoindre notre équipe.");
                $jobPost->setLocation($profile->getLocation());
                $jobPost->setContractType($contractTypes[array_rand($contractTypes)]);
                $jobPost->setSalary(rand(35, 75));
                
                // Attribution aléatoire de 2-4 langages requis
                $requiredSkills = [];
                $numSkills = rand(2, 4);
                $shuffledLanguages = $languages;
                shuffle($shuffledLanguages);
                for ($k = 0; $k < $numSkills; $k++) {
                    $requiredSkills[] = $shuffledLanguages[$k];
                }
                $jobPost->setProgrammingLanguages($requiredSkills);
                
                $jobPost->setExperienceLevel(rand(1, 8));
                $jobPost->setCompany($profile);

                $this->entityManager->persist($jobPost);
            }
            
            $io->text("Entreprise $i et ses offres créées");
        }

        $this->entityManager->flush();

        $io->success('Les données de test ont été générées avec succès !');

        return Command::SUCCESS;
    }
}
