<?php

namespace App\Controller;

use App\Repository\SearchHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/search-history')]
#[IsGranted('ROLE_USER')]
class SearchHistoryController extends AbstractController
{
    public function __construct(
        private SearchHistoryRepository $searchHistoryRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_search_history_index', methods: ['GET'])]
    public function index(): Response
    {
        $searchHistory = $this->searchHistoryRepository->findBy(
            ['user' => $this->getUser()],
            ['searchedAt' => 'DESC']
        );

        return $this->render('search_history/index.html.twig', [
            'search_history' => $searchHistory,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_search_history_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $searchHistory = $this->searchHistoryRepository->find($id);

        if (!$searchHistory) {
            throw $this->createNotFoundException('Historique non trouvé');
        }

        // Vérification de sécurité
        if ($searchHistory->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet historique');
        }

        $this->entityManager->remove($searchHistory);
        $this->entityManager->flush();

        $this->addFlash('success', 'Historique supprimé avec succès');
        return $this->redirectToRoute('app_search_history_index');
    }

    #[Route('/clear', name: 'app_search_history_clear', methods: ['POST'])]
    public function clearAll(): Response
    {
        $histories = $this->searchHistoryRepository->findBy(['user' => $this->getUser()]);
        
        foreach ($histories as $history) {
            $this->entityManager->remove($history);
        }
        
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Historique entièrement effacé');
        return $this->redirectToRoute('app_search_history_index');
    }
}
