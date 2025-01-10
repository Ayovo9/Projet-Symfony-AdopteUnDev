<?php

namespace App\EventSubscriber;

use App\Event\SearchEvent;
use App\Service\SearchHistoryService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchHistorySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SearchHistoryService $searchHistoryService,
        private Security $security
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SearchEvent::class => 'onSearch',
        ];
    }

    public function onSearch(SearchEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $this->searchHistoryService->logSearch(
            $user,
            $event->getQuery(),
            $event->getFilters()
        );
    }
}
