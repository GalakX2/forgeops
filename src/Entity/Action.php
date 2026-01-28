<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
class Action
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $command = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['todo', 'done', 'skipped'])]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Incident $incident = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateIncidentImpact(ExecutionContextInterface $context): void
    {
        // Règle : si incident non null, l'incident doit impacter le service de l'action
        if ($this->incident && $this->service) {
            if (!$this->incident->getServices()->contains($this->service)) {
                $context->buildViolation('L\'incident sélectionné n\'impacte pas le service de cette action.')
                    ->atPath('incident')
                    ->addViolation();
            }
        }
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getCommand(): ?string { return $this->command; }
    public function setCommand(string $command): static { $this->command = $command; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getService(): ?Service { return $this->service; }
    public function setService(?Service $service): static { $this->service = $service; return $this; }
    public function getIncident(): ?Incident { return $this->incident; }
    public function setIncident(?Incident $incident): static { $this->incident = $incident; return $this; }
}