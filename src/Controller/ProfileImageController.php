<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Form\ProfileImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileImageController extends AbstractController
{
    #[Route('/profile/upload-image', name: 'app_profile_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $developerProfile = $user->getDeveloperProfile();
        
        if (!$developerProfile) {
            $developerProfile = new DeveloperProfile();
            $developerProfile->setUser($user);
            $user->setDeveloperProfile($developerProfile);
        }

        $form = $this->createForm(ProfileImageType::class, $developerProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('avatar')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    $oldImage = $developerProfile->getAvatar();
                    if ($oldImage) {
                        $oldImagePath = $this->getParameter('profile_images_directory').'/'.$oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $developerProfile->setAvatar($newFilename);
                    $entityManager->persist($developerProfile);
                    $entityManager->flush();

                    $this->addFlash('success', 'Votre photo de profil a été mise à jour avec succès.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                }
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
