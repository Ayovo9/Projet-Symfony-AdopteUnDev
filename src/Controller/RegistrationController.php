<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\DeveloperProfile;
use App\Entity\CompanyProfile;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Get form data
                $role = $form->get('role')->getData();
                $username = $form->get('username')->getData();
                $email = $form->get('email')->getData();

                $user->setUsername($username);
                $user->setEmail($email);
                $user->setRoles([$role]);

                // Hash the password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                // Create corresponding profile based on role
                if ($role === 'ROLE_COMPANY') {
                    $profile = new CompanyProfile();
                    $profile->setName($username);
                    $profile->setDescription('À compléter');
                    $profile->setLocation('À compléter');
                    $profile->setWebsite('À compléter');
                    $user->setCompanyProfile($profile);
                } else {
                    $profile = new DeveloperProfile();
                    // Extraire le prénom et le nom du username
                    $nameParts = explode(' ', $username, 2);
                    $firstName = $nameParts[0] ?? $username;
                    $lastName = $nameParts[1] ?? 'À compléter';
                    
                    $profile->setFirstName($firstName);
                    $profile->setLastName($lastName);
                    $profile->setLocation('À compléter');
                    $profile->setProgrammingLanguages([]);
                    $profile->setExperienceLevel(0);
                    $profile->setPreferredContractType('CDI');
                    $profile->setBio('À compléter');
                    $user->setDeveloperProfile($profile);
                }

                $entityManager->persist($profile);
                $entityManager->persist($user);

                $logger->info('User object prepared', [
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles()
                ]);

                $entityManager->flush();

                $this->addFlash('success', 'Votre compte a été créé avec succès ! Veuillez vous connecter pour compléter votre profil.');

                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $logger->error('Error during registration', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte.');
                return $this->redirectToRoute('app_register');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
