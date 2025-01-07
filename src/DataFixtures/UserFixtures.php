<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\DeveloperProfile;
use App\Entity\CompanyProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un développeur
        $devUser = new User();
        $devUser->setEmail('dev@example.com');
        $devUser->setUsername('John Doe');
        $devUser->setRoles(['ROLE_DEVELOPER']);
        $devUser->setPassword($this->passwordHasher->hashPassword($devUser, 'password123'));

        $devProfile = new DeveloperProfile();
        $devProfile->setFirstName('John');
        $devProfile->setLastName('Doe');
        $devProfile->setLocation('Paris');
        $devProfile->setProgrammingLanguages(['PHP', 'JavaScript', 'Python']);
        $devProfile->setExperienceLevel(3);
        $devProfile->setPreferredContractType('CDI');
        $devProfile->setBio('Développeur Full Stack passionné');
        
        $devUser->setDeveloperProfile($devProfile);
        $manager->persist($devUser);
        $manager->persist($devProfile);

        // Création d'une entreprise
        $companyUser = new User();
        $companyUser->setEmail('company@example.com');
        $companyUser->setUsername('Tech Company');
        $companyUser->setRoles(['ROLE_COMPANY']);
        $companyUser->setPassword($this->passwordHasher->hashPassword($companyUser, 'password123'));

        $companyProfile = new CompanyProfile();
        $companyProfile->setName('Tech Company');
        $companyProfile->setDescription('Une entreprise innovante');
        $companyProfile->setLocation('Paris');
        $companyProfile->setWebsite('https://example.com');
        
        $companyUser->setCompanyProfile($companyProfile);
        $manager->persist($companyUser);
        $manager->persist($companyProfile);

        $manager->flush();
    }
}
