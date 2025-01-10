<?php

namespace App\Controller;

use App\Service\NotificationService;
use App\Service\SearchHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService,
        private SearchHistoryService $searchHistoryService
    ) {}

    #[Route('', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        $notifications = $this->notificationService->getLatestNotifications($user);
        $recentSearches = $this->searchHistoryService->getRecentSearches($user);
        $unreadNotificationsCount = $this->notificationService->getUnreadCount($user);

        return $this->render('dashboard/index.html.twig', [
            'notifications' => $notifications,
            'recentSearches' => $recentSearches,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    #[Route('/notifications/mark-read', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markNotificationsAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead($this->getUser());
        return $this->json(['success' => true]);
    }

    #[Route('/search-history/clear', name: 'app_search_history_clear', methods: ['POST'])]
    public function clearSearchHistory(): JsonResponse
    {
        $this->searchHistoryService->clearHistory($this->getUser());
        return $this->json(['success' => true]);
    }

    #[Route('/notifications/count', name: 'app_notifications_count', methods: ['GET'])]
    public function getUnreadNotificationsCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount($this->getUser());
        return $this->json(['count' => $count]);
    }
}
