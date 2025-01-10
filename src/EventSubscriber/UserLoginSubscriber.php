<?php

namespace App\EventSubscriber;

use App\Service\MatchingNotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MatchingNotificationService $matchingNotificationService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginAt(new \DateTime());

        // Si c'est un développeur, vérifier les nouvelles offres correspondantes
        if ($user->getDeveloperProfile()) {
            $this->matchingNotificationService->checkNewMatchesForDeveloper($user->getDeveloperProfile());
        }

        $this->entityManager->flush();
    }
}
