<?php

namespace App\Service;

use App\Entity\SearchHistory;
use App\Entity\User;
use App\Repository\SearchHistoryRepository;

class SearchHistoryService
{
    public function __construct(
        private SearchHistoryRepository $searchHistoryRepository
    ) {
    }

    public function logSearch(User $user, string $query, array $filters = []): void
    {
        $searchHistory = new SearchHistory();
        $searchHistory->setUser($user)
            ->setSearchQuery($query)
            ->setFilters($filters);

        $this->searchHistoryRepository->save($searchHistory);
    }
}
