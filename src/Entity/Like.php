<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Bloop $bloop = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Comment $comment = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?Audio $podcast = null;

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

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

    public function getPodcast(): ?Audio
    {
        return $this->podcast;
    }

    public function setPodcast(?Audio $podcast): static
    {
        $this->podcast = $podcast;

        return $this;
    }
}
