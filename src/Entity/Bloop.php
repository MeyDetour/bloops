<?php

namespace App\Entity;

use App\Repository\BloopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BloopRepository::class)]
class Bloop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'bloop', orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'bloop', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;



    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'bloop')]
    private Collection $videos;

    #[ORM\Column]
    private ?bool $displayComments = null;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'bloop')]
    private Collection $likes;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $chapo = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $status = null;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'bloop', orphanRemoval: true)]
    private Collection $reports;






    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();


        $this->videos = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $images): static
    {
        if (!$this->images->contains($images)) {
            $this->images->add($images);
            $images->setBloop($this);
        }

        return $this;
    }

    public function removeImage(Image $images): static
    {
        if ($this->images->removeElement($images)) {
            // set the owning side to null (unless already changed)
            if ($images->getBloop() === $this) {
                $images->setBloop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBloop($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBloop() === $this) {
                $comment->setBloop(null);
            }
        }

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



    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setBloop($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getBloop() === $this) {
                $video->setBloop(null);
            }
        }

        return $this;
    }

    public function isDisplayComments(): ?bool
    {
        return $this->displayComments;
    }

    public function setDisplayComments(bool $displayComments): static
    {
        $this->displayComments = $displayComments;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setBloop($this);
        }

        return $this;
    }
    public function isLikedBy(User $user)
    {
        $isLikedBy = false;
        foreach ($this->likes as $like){
            if($like->getAuthor() == $user){
                return true;
            }
        }
        return $isLikedBy;
    }
    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getBloop() === $this) {
                $like->setBloop(null);
            }
        }
        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(string $chapo): static
    {
        $this->chapo = $chapo;

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setBloop($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getBloop() === $this) {
                $report->setBloop(null);
            }
        }

        return $this;
    }




}
