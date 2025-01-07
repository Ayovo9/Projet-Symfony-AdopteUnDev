<?php

namespace App\Controller;

use App\Entity\DeveloperProfile;
use App\Entity\DeveloperRating;
use App\Form\DeveloperSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/developer/social')]
class DeveloperSocialController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_developer_social')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DeveloperSearchType::class);
        $form->handleRequest($request);

        $developers = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $searchTerm = $form->get('search')->getData();
            $developers = $this->entityManager->getRepository(DeveloperProfile::class)
                ->createQueryBuilder('d')
                ->where('CONCAT(d.firstName, \' \', d.lastName) LIKE :search')
                ->orWhere('d.firstName LIKE :search')
                ->orWhere('d.lastName LIKE :search')
                ->orWhere('CONCAT(\'dev\', d.id) LIKE :exactSearch')
                ->setParameter('search', '%' . $searchTerm . '%')
                ->setParameter('exactSearch', $searchTerm)
                ->getQuery()
                ->getResult();
        }

        return $this->render('developer/social/index.html.twig', [
            'form' => $form->createView(),
            'developers' => $developers
        ]);
    }

    #[Route('/rate/{id}', name: 'app_developer_rate', methods: ['POST'])]
    public function rateDeveloper(Request $request, DeveloperProfile $developer): JsonResponse
    {
        try {
            if (!$this->getUser() || !$this->getUser()->getDeveloperProfile()) {
                return new JsonResponse(['error' => 'Vous devez être connecté en tant que développeur.'], 403);
            }

            if ($developer === $this->getUser()->getDeveloperProfile()) {
                return new JsonResponse(['error' => 'Vous ne pouvez pas vous noter vous-même.'], 400);
            }

            $rating = $request->request->get('rating');
            if (!is_numeric($rating)) {
                return new JsonResponse(['error' => 'La note doit être un nombre.'], 400);
            }

            $rating = (int) $rating;
            if ($rating < 1 || $rating > 5) {
                return new JsonResponse(['error' => 'La note doit être comprise entre 1 et 5.'], 400);
            }

            // Vérifier si une note existe déjà
            $existingRating = $this->entityManager->getRepository(DeveloperRating::class)
                ->findOneBy([
                    'ratingDeveloper' => $this->getUser()->getDeveloperProfile(),
                    'ratedDeveloper' => $developer
                ]);

            if ($existingRating) {
                return new JsonResponse(['error' => 'Vous avez déjà noté ce développeur.'], 400);
            }

            // Créer la nouvelle note
            $newRating = new DeveloperRating();
            $newRating->setRatingDeveloper($this->getUser()->getDeveloperProfile());
            $newRating->setRatedDeveloper($developer);
            $newRating->setRating($rating);

            $this->entityManager->persist($newRating);
            $this->entityManager->flush();

            // Calculer la nouvelle moyenne
            $averageRating = $this->entityManager->getRepository(DeveloperRating::class)
                ->createQueryBuilder('r')
                ->select('AVG(r.rating)')
                ->where('r.ratedDeveloper = :developer')
                ->setParameter('developer', $developer)
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            return new JsonResponse([
                'success' => true,
                'averageRating' => number_format(round($averageRating, 1), 1)
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors de la notation : ' . $e->getMessage()
            ], 500);
        }
    }
}
