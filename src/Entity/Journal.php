<?php

namespace App\Entity;

use App\Repository\JournalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: JournalRepository::class)]
class Journal
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['detected', 'investigation', 'monitoring', 'recovered', 'postmortem'])]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $occurredAt = null;

    #[ORM\ManyToOne(inversedBy: 'journals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Incident $incident = null;

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if (!$this->incident) return;

        if ($this->occurredAt < $this->incident->getStartedAt()) {
             $context->buildViolation('Le journal ne peut pas précéder le début de l\'incident.')
                ->atPath('occurredAt')->addViolation();
        }

        if ($this->incident->getResolvedAt() && $this->occurredAt > $this->incident->getResolvedAt()) {
             $context->buildViolation('Le journal ne peut pas être après la résolution.')
                ->atPath('occurredAt')->addViolation();
        }
    }

    public function getId(): ?int { return $this->id; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }
    public function getOccurredAt(): ?\DateTimeInterface { return $this->occurredAt; }
    public function setOccurredAt(\DateTimeInterface $occurredAt): static { $this->occurredAt = $occurredAt; return $this; }
    public function getIncident(): ?Incident { return $this->incident; }
    public function setIncident(?Incident $incident): static { $this->incident = $incident; return $this; }
}