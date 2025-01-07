<?php

namespace App\Entity;

use App\Repository\DeveloperProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DeveloperProfileRepository::class)]
class DeveloperProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'developerProfile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(type: 'json')]
    private array $programmingLanguages = [];

    #[ORM\Column(type: 'integer')]
    private ?int $experienceLevel = null;

    #[ORM\Column(length: 255)]
    private ?string $preferredContractType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $github = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minimumSalary = null;

    #[ORM\Column]
    private int $viewCount = 0;

    #[ORM\Column(type: 'datetime', options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'developer', targetEntity: JobMatch::class, orphanRemoval: true)]
    private Collection $matches;

    #[ORM\OneToMany(mappedBy: 'ratedDeveloper', targetEntity: DeveloperRating::class, orphanRemoval: true)]
    private Collection $ratingsReceived;

    #[ORM\OneToMany(mappedBy: 'ratingDeveloper', targetEntity: DeveloperRating::class, orphanRemoval: true)]
    private Collection $ratingsGiven;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->ratingsReceived = new ArrayCollection();
        $this->ratingsGiven = new ArrayCollection();
        $this->viewCount = 0;
        $this->createdAt = new \DateTime();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
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

    public function getProgrammingLanguages(): array
    {
        return $this->programmingLanguages;
    }

    public function setProgrammingLanguages(array $programmingLanguages): static
    {
        $this->programmingLanguages = $programmingLanguages;
        return $this;
    }

    public function getExperienceLevel(): ?int
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(int $experienceLevel): static
    {
        $this->experienceLevel = $experienceLevel;
        return $this;
    }

    public function getPreferredContractType(): ?string
    {
        return $this->preferredContractType;
    }

    public function setPreferredContractType(string $preferredContractType): static
    {
        $this->preferredContractType = $preferredContractType;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): static
    {
        $this->github = $github;
        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    public function getMinimumSalary(): ?int
    {
        return $this->minimumSalary;
    }

    public function setMinimumSalary(?int $minimumSalary): static
    {
        $this->minimumSalary = $minimumSalary;
        return $this;
    }

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function incrementViewCount(): self
    {
        $this->viewCount++;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, JobMatch>
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(JobMatch $match): static
    {
        if (!$this->matches->contains($match)) {
            $this->matches->add($match);
            $match->setDeveloper($this);
        }

        return $this;
    }

    public function removeMatch(JobMatch $match): static
    {
        if ($this->matches->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getDeveloper() === $this) {
                $match->setDeveloper(null);
            }
        }

        return $this;
    }

    public function getRatingsReceived(): Collection
    {
        return $this->ratingsReceived;
    }

    public function getAverageRating(): float
    {
        if ($this->ratingsReceived->isEmpty()) {
            return 0.0;
        }

        $sum = 0;
        foreach ($this->ratingsReceived as $rating) {
            $sum += $rating->getRating();
        }

        return $sum / $this->ratingsReceived->count();
    }

    public function getRatingsGiven(): Collection
    {
        return $this->ratingsGiven;
    }
}
