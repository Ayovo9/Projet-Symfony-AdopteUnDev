<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\DeveloperProfile;
use App\Entity\JobPost;
use App\Repository\FavoriteRepository;
use App\Repository\DeveloperProfileRepository;
use App\Repository\JobPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/favorite')]
#[IsGranted('ROLE_USER')]
class FavoriteController extends AbstractController
{
    private $entityManager;
    private $favoriteRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FavoriteRepository $favoriteRepository
    ) {
        $this->entityManager = $entityManager;
        $this->favoriteRepository = $favoriteRepository;
    }

    #[Route('/toggle/developer/{id}', name: 'app_favorite_toggle_developer', methods: ['POST'])]
    public function toggleDeveloperFavorite(
        DeveloperProfile $developer,
        Request $request
    ): JsonResponse {
        $user = $this->getUser();
        
        // Vérifier si déjà en favoris
        $existingFavorite = $this->favoriteRepository->findOneBy([
            'user' => $user,
            'developer' => $developer
        ]);

        if ($existingFavorite) {
            $this->entityManager->remove($existingFavorite);
            $status = 'removed';
        } else {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setDeveloper($developer);
            $this->entityManager->persist($favorite);
            $status = 'added';
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'status' => $status
        ]);
    }

    #[Route('/toggle/job/{id}', name: 'app_favorite_toggle_job', methods: ['POST'])]
    public function toggleJobFavorite(
        JobPost $jobPost,
        Request $request
    ): JsonResponse {
        $user = $this->getUser();
        
        // Vérifier si déjà en favoris
        $existingFavorite = $this->favoriteRepository->findOneBy([
            'user' => $user,
            'jobPost' => $jobPost
        ]);

        if ($existingFavorite) {
            $this->entityManager->remove($existingFavorite);
            $status = 'removed';
        } else {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setJobPost($jobPost);
            $this->entityManager->persist($favorite);
            $status = 'added';
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'status' => $status
        ]);
    }

    #[Route('/list', name: 'app_favorite_list')]
    public function list(): Response
    {
        if ($this->isGranted('ROLE_COMPANY')) {
            return $this->redirectToRoute('app_company_favorites');
        }
        
        $favorites = $this->favoriteRepository->findUserFavorites($this->getUser());

        return $this->render('favorite/list.html.twig', [
            'favorites' => $favorites
        ]);
    }

    #[Route('/company', name: 'app_company_favorites')]
    #[IsGranted('ROLE_COMPANY')]
    public function companyFavorites(): Response
    {
        $user = $this->getUser();
        $favorites = $this->favoriteRepository->findBy(['user' => $user]);

        $developers = [];
        foreach ($favorites as $favorite) {
            if ($favorite->getDeveloper()) {
                $developers[] = $favorite->getDeveloper();
            }
        }

        return $this->render('favorite/companylist.html.twig', [
            'developers' => $developers
        ]);
    }
}
