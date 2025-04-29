<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true)]
    private ?User $updatedBy = null;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Cursus::class, cascade: ['persist', 'remove'])]
    private Collection $cursuses;

    public function __construct()
    {
        $this->cursuses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * @return Collection<int, Cursus>
     */
    public function getCursuses(): Collection
    {
        return $this->cursuses;
    }

    public function addCursus(Cursus $cursus): static
    {
        if (!$this->cursuses->contains($cursus)) {
            $this->cursuses->add($cursus);
            $cursus->setTheme($this);
        }
        return $this;
    }

    public function removeCursus(Cursus $cursus): static
    {
        if ($this->cursuses->removeElement($cursus)) {
            if ($cursus->getTheme() === $this) {
                $cursus->setTheme(null);
            }
        }
        return $this;
    }

    // Gestion automatique des dates si tu utilises Doctrine Events
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}