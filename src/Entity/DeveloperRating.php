<?php

namespace App\Entity;

use App\Repository\DeveloperRatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeveloperRatingRepository::class)]
class DeveloperRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ratingsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeveloperProfile $ratedDeveloper = null;

    #[ORM\ManyToOne(inversedBy: 'ratingsGiven')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeveloperProfile $ratingDeveloper = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatedDeveloper(): ?DeveloperProfile
    {
        return $this->ratedDeveloper;
    }

    public function setRatedDeveloper(?DeveloperProfile $ratedDeveloper): static
    {
        $this->ratedDeveloper = $ratedDeveloper;

        return $this;
    }

    public function getRatingDeveloper(): ?DeveloperProfile
    {
        return $this->ratingDeveloper;
    }

    public function setRatingDeveloper(?DeveloperProfile $ratingDeveloper): static
    {
        $this->ratingDeveloper = $ratingDeveloper;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
