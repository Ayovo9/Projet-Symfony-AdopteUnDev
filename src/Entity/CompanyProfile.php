<?php

namespace App\Entity;

use App\Repository\CompanyProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CompanyProfileRepository::class)]
class CompanyProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'companyProfile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $industry = null;

    #[ORM\Column(nullable: true)]
    private ?int $companySize = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: JobPost::class, orphanRemoval: true)]
    private Collection $jobPosts;

    public function __construct()
    {
        $this->jobPosts = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): static
    {
        $this->website = $website;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function getIndustry(): ?string
    {
        return $this->industry;
    }

    public function setIndustry(?string $industry): static
    {
        $this->industry = $industry;
        return $this;
    }

    public function getCompanySize(): ?int
    {
        return $this->companySize;
    }

    public function setCompanySize(?int $companySize): static
    {
        $this->companySize = $companySize;
        return $this;
    }

    /**
     * @return Collection<int, JobPost>
     */
    public function getJobPosts(): Collection
    {
        return $this->jobPosts;
    }

    public function addJobPost(JobPost $jobPost): static
    {
        if (!$this->jobPosts->contains($jobPost)) {
            $this->jobPosts->add($jobPost);
            $jobPost->setCompany($this);
        }

        return $this;
    }

    public function removeJobPost(JobPost $jobPost): static
    {
        if ($this->jobPosts->removeElement($jobPost)) {
            // set the owning side to null (unless already changed)
            if ($jobPost->getCompany() === $this) {
                $jobPost->setCompany(null);
            }
        }

        return $this;
    }
}
