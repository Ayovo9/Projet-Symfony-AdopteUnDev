<?php

namespace App\Entity;

use App\Repository\ProfileViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileViewRepository::class)]
class ProfileView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DeveloperProfile::class)]
    #[ORM\JoinColumn(name: 'developer_id', referencedColumnName: 'id', nullable: false)]
    private ?DeveloperProfile $developer = null;

    #[ORM\ManyToOne(targetEntity: CompanyProfile::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false)]
    private ?CompanyProfile $company = null;

    #[ORM\ManyToOne(targetEntity: JobPost::class)]
    #[ORM\JoinColumn(name: 'job_post_id', referencedColumnName: 'id', nullable: true, onDelete: "SET NULL")]
    private ?JobPost $jobPost = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $viewedAt = null;

    public function __construct()
    {
        $this->viewedAt = new \DateTimeImmutable();
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

    public function getCompany(): ?CompanyProfile
    {
        return $this->company;
    }

    public function setCompany(?CompanyProfile $company): static
    {
        $this->company = $company;
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

    public function getViewedAt(): ?\DateTimeImmutable
    {
        return $this->viewedAt;
    }

    public function setViewedAt(\DateTimeImmutable $viewedAt): static
    {
        $this->viewedAt = $viewedAt;
        return $this;
    }
}
