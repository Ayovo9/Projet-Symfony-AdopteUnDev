<?php

namespace App\Twig;

use App\Entity\DeveloperProfile;
use App\Entity\JobPost;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FavoriteExtension extends AbstractExtension
{
    private $favoriteRepository;
    private $security;

    public function __construct(FavoriteRepository $favoriteRepository, Security $security)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_favorite', [$this, 'isFavorite']),
        ];
    }

    public function isFavorite($item): bool
    {
        $user = $this->security->getUser();
        if (!$user) {
            return false;
        }

        if (!($item instanceof DeveloperProfile) && !($item instanceof JobPost)) {
            return false;
        }

        return $this->favoriteRepository->isInFavorites($user, $item);
    }
}
