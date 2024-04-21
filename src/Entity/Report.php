<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bloop $bloop = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'reportTarget')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $targetAuthor = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBloop(): ?Bloop
    {
        return $this->bloop;
    }

    public function setBloop(?Bloop $bloop): static
    {
        $this->bloop = $bloop;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTargetAuthor(): ?User
    {
        return $this->targetAuthor;
    }

    public function setTargetAuthor(?User $targetAuthor): static
    {
        $this->targetAuthor = $targetAuthor;

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


}
