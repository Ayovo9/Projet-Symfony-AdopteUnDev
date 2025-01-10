<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function createNotification(
        User $user,
        string $type,
        string $message,
        array $data = []
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user)
            ->setType($type)
            ->setMessage($message)
            ->setData($data);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    public function markAllAsRead(User $user): void
    {
        $this->entityManager->getRepository(Notification::class)
            ->markAllAsRead($user);
    }

    public function getUnreadCount(User $user): int
    {
        return $this->entityManager->getRepository(Notification::class)
            ->getUnreadNotificationsCount($user);
    }

    public function getLatestNotifications(User $user, int $limit = 10): array
    {
        return $this->entityManager->getRepository(Notification::class)
            ->getLatestNotifications($user, $limit);
    }

    public function getNotification(int $id): ?Notification
    {
        return $this->entityManager->getRepository(Notification::class)->find($id);
    }
}
