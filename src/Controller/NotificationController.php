<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository
    ) {
    }

    #[Route('', name: 'app_notifications_index', methods: ['GET'])]
    public function index(): Response
    {
        $notifications = $this->notificationRepository->findRecentByUser($this->getUser());

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/unread', name: 'app_notifications_unread', methods: ['GET'])]
    public function unread(): JsonResponse
    {
        $notifications = $this->notificationRepository->findUnreadByUser($this->getUser());

        $data = array_map(function($notification) {
            return [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'data' => $notification->getData(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $notifications);

        return $this->json($data);
    }

    #[Route('/{id}/mark-read', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(int $id): JsonResponse
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification || $notification->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Notification not found');
        }

        $this->notificationRepository->markAsRead($notification);

        return $this->json(['success' => true]);
    }
}
