<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?DeveloperProfile $developer = null;

    #[ORM\ManyToOne]
    private ?JobPost $jobPost = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDeveloper(): ?DeveloperProfile
    {
        return $this->developer;
    }

    public function setDeveloper(?DeveloperProfile $developer): static
    {
        $this->developer = $developer;
        return $this;
    }

    public function getJobPost(): ?JobPost
    {
        return $this->jobPost;
    }

    public function setJobPost(?JobPost $jobPost): static
    {
        $this->jobPost = $jobPost;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
