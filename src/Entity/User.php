<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?DeveloperProfile $developerProfile = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?CompanyProfile $companyProfile = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: JobPost::class, cascade: ['persist', 'remove'])]
    private Collection $jobPosts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->jobPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function getDeveloperProfile(): ?DeveloperProfile
    {
        return $this->developerProfile;
    }

    public function setDeveloperProfile(?DeveloperProfile $developerProfile): static
    {
        if ($developerProfile === null && $this->developerProfile !== null) {
            $this->developerProfile->setUser(null);
        }

        if ($developerProfile !== null && $developerProfile->getUser() !== $this) {
            $developerProfile->setUser($this);
        }

        $this->developerProfile = $developerProfile;
        return $this;
    }

    public function getCompanyProfile(): ?CompanyProfile
    {
        return $this->companyProfile;
    }

    public function setCompanyProfile(?CompanyProfile $companyProfile): static
    {
        if ($companyProfile === null && $this->companyProfile !== null) {
            $this->companyProfile->setUser(null);
        }

        if ($companyProfile !== null && $companyProfile->getUser() !== $this) {
            $companyProfile->setUser($this);
        }

        $this->companyProfile = $companyProfile;
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
            $jobPost->setUser($this);
        }

        return $this;
    }

    public function removeJobPost(JobPost $jobPost): static
    {
        if ($this->jobPosts->removeElement($jobPost)) {
            // set the owning side to null (unless already changed)
            if ($jobPost->getUser() === $this) {
                $jobPost->setUser(null);
            }
        }

        return $this;
    }
}
