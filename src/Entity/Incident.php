<?php

namespace App\Entity;

use App\Repository\IncidentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: IncidentRepository::class)]
class Incident
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['open', 'monitoring', 'resolved'])]
    private ?string $status = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(choices: ['sev1', 'sev2', 'sev3'])]
    private ?string $severity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resolvedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $summary = null;

    #[ORM\ManyToMany(targetEntity: Service::class)]
    #[Assert\Count(min: 1, minMessage: "Au moins un service doit être impacté.")]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'incident', targetEntity: Journal::class, orphanRemoval: true)]
    private Collection $journals;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->journals = new ArrayCollection();
    }

    // REGLES METIER
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if ($this->status === 'resolved' && $this->resolvedAt === null) {
            $context->buildViolation('La date de résolution est obligatoire si le statut est resolved.')
                ->atPath('resolvedAt')->addViolation();
        }

        if ($this->status !== 'resolved' && $this->resolvedAt !== null) {
            $context->buildViolation('La date de résolution doit être vide si l\'incident n\'est pas résolu.')
                ->atPath('resolvedAt')->addViolation();
        }

        if ($this->resolvedAt && $this->startedAt > $this->resolvedAt) {
            $context->buildViolation('La date de début doit être antérieure à la date de résolution.')
                ->atPath('startedAt')->addViolation();
        }
    }

    // Getters / Setters essentiels
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getSeverity(): ?string { return $this->severity; }
    public function setSeverity(string $severity): static { $this->severity = $severity; return $this; }
    public function getStartedAt(): ?\DateTimeInterface { return $this->startedAt; }
    public function setStartedAt(\DateTimeInterface $startedAt): static { $this->startedAt = $startedAt; return $this; }
    public function getResolvedAt(): ?\DateTimeInterface { return $this->resolvedAt; }
    public function setResolvedAt(?\DateTimeInterface $resolvedAt): static { $this->resolvedAt = $resolvedAt; return $this; }
    public function getSummary(): ?string { return $this->summary; }
    public function setSummary(string $summary): static { $this->summary = $summary; return $this; }
    
    public function getServices(): Collection { return $this->services; }
    public function addService(Service $service): static {
        if (!$this->services->contains($service)) { $this->services->add($service); }
        return $this;
    }
    public function removeService(Service $service): static {
        $this->services->removeElement($service);
        return $this;
    }

    public function getJournals(): Collection { return $this->journals; }
    
    public function __toString(): string { return $this->title; }
}