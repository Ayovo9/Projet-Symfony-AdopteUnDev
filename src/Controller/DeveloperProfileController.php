<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\DeveloperProfileType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\DeveloperRatingRepository;

#[Route('/developer/profile')]
class DeveloperProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_developer_profile')]
    public function index(DeveloperRatingRepository $ratingRepo): Response
    {
        $developer = $this->getUser()->getDeveloperProfile();
        
        if (!$developer) {
            return $this->redirectToRoute('app_developer_profile_create');
        }

        $averageRating = $ratingRepo->getAverageRating($developer);

        return $this->render('developer/profile/show.html.twig', [
            'developer' => $developer,
            'averageRating' => $averageRating,
            'isOwnProfile' => true
        ]);
    }

    #[Route('/create', name: 'app_developer_profile_create')]
    public function create(Request $request, FileUploader $fileUploader): Response
    {
        // Vérifier si l'utilisateur a déjà un profil
        if ($this->getUser()->getDeveloperProfile()) {
            return $this->redirectToRoute('app_developer_profile');
        }

        $profile = new DeveloperProfile();
        $profile->setUser($this->getUser());
        
        $form = $this->createForm(DeveloperProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                $avatarFileName = $fileUploader->upload($avatarFile);
                $profile->setAvatar($avatarFileName);
            }

            $this->entityManager->persist($profile);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été créé avec succès !');
            return $this->redirectToRoute('app_developer_profile');
        }

        return $this->render('developer/profile/edit.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
            'isNewProfile' => true
        ]);
    }

    #[Route('/edit', name: 'app_developer_profile_edit')]
    public function edit(Request $request, FileUploader $fileUploader): Response
    {
        $profile = $this->getUser()->getDeveloperProfile();
        
        if (!$profile) {
            return $this->redirectToRoute('app_developer_profile_create');
        }

        $form = $this->createForm(DeveloperProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                // Supprimer l'ancien avatar s'il existe
                if ($profile->getAvatar()) {
                    $oldAvatarPath = $fileUploader->getTargetDirectory() . '/' . $profile->getAvatar();
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }

                $avatarFileName = $fileUploader->upload($avatarFile);
                $profile->setAvatar($avatarFileName);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_developer_profile');
        }

        return $this->render('developer/profile/edit.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
            'isNewProfile' => false
        ]);
    }

    #[Route('/{id}', name: 'app_developer_profile_view')]
    public function view(DeveloperProfile $developer, DeveloperRatingRepository $ratingRepo): Response
    {
        $averageRating = $ratingRepo->getAverageRating($developer);
        
        // Vérifie si c'est le profil de l'utilisateur connecté
        $isOwnProfile = false;
        if ($this->getUser() && $this->getUser()->getDeveloperProfile() === $developer) {
            $isOwnProfile = true;
        }

        // Incrémente le compteur de vues si ce n'est pas le propre profil de l'utilisateur
        if (!$isOwnProfile) {
            $developer->incrementViewCount();
            $this->entityManager->flush();
        }

        return $this->render('developer/profile/show.html.twig', [
            'developer' => $developer,
            'averageRating' => $averageRating,
            'isOwnProfile' => $isOwnProfile
        ]);
    }

    #[Route('/{id}', name: 'app_developer_profile_show', methods: ['GET'])]
    public function show(
        DeveloperProfile $developer,
        EntityManagerInterface $entityManager,
        DeveloperRatingRepository $ratingRepo
    ): Response {
        // Increment view count
        $developer->incrementViewCount();
        $entityManager->flush();

        $averageRating = $ratingRepo->getAverageRating($developer);

        return $this->render('developer/profile/show.html.twig', [
            'developer' => $developer,
            'averageRating' => $averageRating
        ]);
    }
}
