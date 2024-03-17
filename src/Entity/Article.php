<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 4,
        max: 300,
        minMessage: 'Your title must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles')]
    private Collection $categories;

    #[ORM\OneToMany(targetEntity: Audio::class, mappedBy: 'article')]
    private Collection $audio;

    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'article')]
    private Collection $videos;

    #[ORM\Column]
    private ?bool $displayComments = null;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'article')]
    private Collection $likes;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $chapo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $first_subtitle = null;

    #[ORM\Column(type: Types::TEXT , nullable : true)]
    private ?string $first_paragraphe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $second_subtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $second_paragraphe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $third_subtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $third_paragraphe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $status = null;





    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->audio = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->likes = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
            $images->setArticle($this);
        }

        return $this;
    }

    public function removeImage(Image $images): static
    {
        if ($this->images->removeElement($images)) {
            // set the owning side to null (unless already changed)
            if ($images->getArticle() === $this) {
                $images->setArticle(null);
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
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
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
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Audio>
     */
    public function getAudio(): Collection
    {
        return $this->audio;
    }

    public function addAudio(Audio $audio): static
    {
        if (!$this->audio->contains($audio)) {
            $this->audio->add($audio);
            $audio->setArticle($this);
        }

        return $this;
    }

    public function removeAudio(Audio $audio): static
    {
        if ($this->audio->removeElement($audio)) {
            // set the owning side to null (unless already changed)
            if ($audio->getArticle() === $this) {
                $audio->setArticle(null);
            }
        }

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
            $video->setArticle($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getArticle() === $this) {
                $video->setArticle(null);
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
            $like->setArticle($this);
        }

        return $this;
    }
    public function isLikedBy(User $user)
    {
        $isLikedBy = false;
        foreach ($this->likes as $like){
            if($like->getAuthor == $user){
                return $isLikedBy;
            }
        }
        return $isLikedBy;
    }
    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getArticle() === $this) {
                $like->setArticle(null);
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

    public function getFirstSubtitle(): ?string
    {
        return $this->first_subtitle;
    }

    public function setFirstSubtitle(?string $first_subtitle): static
    {
        $this->first_subtitle = $first_subtitle;

        return $this;
    }

    public function getFirstParagraphe(): ?string
    {
        return $this->first_paragraphe;
    }

    public function setFirstParagraphe(string $first_paragraphe): static
    {
        $this->first_paragraphe = $first_paragraphe;

        return $this;
    }

    public function getSecondSubtitle(): ?string
    {
        return $this->second_subtitle;
    }

    public function setSecondSubtitle(?string $second_subtitle): static
    {
        $this->second_subtitle = $second_subtitle;

        return $this;
    }

    public function getSecondParagraphe(): ?string
    {
        return $this->second_paragraphe;
    }

    public function setSecondParagraphe(?string $second_paragraphe): static
    {
        $this->second_paragraphe = $second_paragraphe;

        return $this;
    }

    public function getThirdSubtitle(): ?string
    {
        return $this->third_subtitle;
    }

    public function setThirdSubtitle(?string $third_subtitle): static
    {
        $this->third_subtitle = $third_subtitle;

        return $this;
    }

    public function getThirdParagraphe(): ?string
    {
        return $this->third_paragraphe;
    }

    public function setThirdParagraphe(?string $third_paragraphe): static
    {
        $this->third_paragraphe = $third_paragraphe;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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


}
