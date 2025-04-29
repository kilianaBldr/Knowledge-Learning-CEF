<?php

namespace App\Entity;

use DateTime;
use DateInterval;
use DateTimeInterface;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[Assert\EqualTo(propertyPath: "password", message: "The password and confirmation password must match.")]
    private $confirmPassword;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $tokenRegistrationLifeTime = null;

    #[ORM\ManyToMany(targetEntity: Lessons::class)]
    #[ORM\JoinTable(name: 'user_lessons')]
    private Collection $purchasedLessons;

    #[ORM\ManyToMany(targetEntity: Cursus::class)]
    #[ORM\JoinTable(name: 'user_cursus')]
    private Collection $purchasedCursus;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->purchasedLessons = new ArrayCollection();
        $this->purchasedCursus = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
        $this->isVerified = false;
        //Définit une expiration du token de confirmation dans 24h
        $this->tokenRegistrationLifeTime = (new Datetime('now'))->add(new DateInterval("P1D"));

    }

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = array_unique($roles);
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function SetConfirmPassword(string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }


    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function getTokenRegistrationLifeTime(): ?\DateTimeInterface
    {
        return $this->tokenRegistrationLifeTime;
    }

    public function setTokenRegistrationLifeTime(\DateTimeInterface $tokenRegistrationLifeTime): static
    {
        $this->tokenRegistrationLifeTime = $tokenRegistrationLifeTime;

        return $this;
    }

    /**
     * @return Collection<int, Lessons>
     */
    public function getPurchasedLessons(): Collection
    {
        return $this->purchasedLessons;
    }

    public function addPurchasedLesson(Lessons $lesson): static
    {
        if (!$this->purchasedLessons->contains($lesson)) {
            $this->purchasedLessons->add($lesson);
        }
        return $this;
    }

    public function removePurchasedLesson(Lessons $lesson): static
    {
        $this->purchasedLessons->removeElement($lesson);
        return $this;
    }

    public function hasPurchasedLesson(Lessons $lesson): bool
    {
        return $this->purchasedLessons->contains($lesson);
    }

    public function getPurchasedCursus(): Collection
    {
        return $this->purchasedCursus;
    }

    public function addPurchasedCursus(Cursus $cursus): static
    {
        if (!$this->purchasedCursus->contains($cursus)) {
            $this->purchasedCursus->add($cursus);
        }
        return $this;
    }


    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this; 
    } 


    public function getCreatedAt(): ?\DateTimeImmutable 
    { 
        return $this->createdAt; 
    } 
    
    public function getUpdatedAt(): ?\DateTimeImmutable 
    { 
        return $this->updatedAt; 
    } 
    
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static 
    { 
        $this->updatedAt = $updatedAt; 
        return $this; 
    }
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles temporaires si nécessaire
    }
}