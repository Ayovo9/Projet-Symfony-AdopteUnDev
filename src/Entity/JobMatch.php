<?php

namespace App\Entity;

use App\Repository\JobMatchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobMatchRepository::class)]
#[ORM\Table(name: 'matches')]
class JobMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeveloperProfile $developer = null;

    #[ORM\ManyToOne(inversedBy: 'matches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobPost $jobPost = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $matchScore = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private bool $isViewed = false;

    #[ORM\Column]
    private bool $isApplied = false;

    #[ORM\Column]
    private bool $isSaved = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMatchScore(): ?string
    {
        return $this->matchScore;
    }

    public function setMatchScore(string $matchScore): static
    {
        $this->matchScore = $matchScore;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isViewed(): bool
    {
        return $this->isViewed;
    }

    public function setIsViewed(bool $isViewed): static
    {
        $this->isViewed = $isViewed;

        return $this;
    }

    public function isApplied(): bool
    {
        return $this->isApplied;
    }

    public function setIsApplied(bool $isApplied): static
    {
        $this->isApplied = $isApplied;

        return $this;
    }

    public function isSaved(): bool
    {
        return $this->isSaved;
    }

    public function setIsSaved(bool $isSaved): static
    {
        $this->isSaved = $isSaved;

        return $this;
    }
}
