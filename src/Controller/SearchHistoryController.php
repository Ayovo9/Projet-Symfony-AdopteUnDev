<?php

namespace App\Controller;

use App\Repository\SearchHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/search-history')]
#[IsGranted('ROLE_USER')]
class SearchHistoryController extends AbstractController
{
    public function __construct(
        private SearchHistoryRepository $searchHistoryRepository
    ) {
    }

    #[Route('', name: 'app_search_history_index', methods: ['GET'])]
    public function index(): Response
    {
        $searchHistory = $this->searchHistoryRepository->findUserHistory($this->getUser());

        return $this->render('search_history/index.html.twig', [
            'search_history' => $searchHistory,
        ]);
    }
}
