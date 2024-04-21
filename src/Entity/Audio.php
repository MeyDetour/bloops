<?php

namespace App\Entity;

use App\Repository\AudioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: AudioRepository::class)]
#[Vich\Uploadable]
class Audio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'audios', fileNameProperty: 'audioName', size: 'audioSize')]
    private ?File $audioFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $audioName = null;

    #[ORM\Column(nullable: true)]
    private ?int $audioSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;



    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'audio')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToOne(targetEntity: Image::class, mappedBy: 'audio')]
    private ?Image $image = null;
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'podcast')]
    private Collection $likes;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'audio')]
    private Collection $comment;

    #[ORM\Column]
    private ?bool $displayComment = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $status = null;

    public function __construct()
    {

        $this->likes = new ArrayCollection();
        $this->comment = new ArrayCollection();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $audioFile
     */
    public function setAudioFile(?File $audioFile = null): void
    {
        $this->audioFile = $audioFile;

        if (null !== $audioFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAudioFile(): ?File
    {
        return $this->audioFile;
    }

    public function setAudioName(?string $audioName): void
    {
        $this->audioName = $audioName;
    }

    public function getAudioName(): ?string
    {
        return $this->audioName;
    }

    public function setAudioSize(?int $audioSize): void
    {
        $this->audioSize = $audioSize;
    }

    public function getAudioSize(): ?int
    {
        return $this->audioSize;
    }



    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }  public function getAudioUrl(): ?string
{
    if ($this->audioName) {
        return 'videos/' . $this->audioName;
    }

    return null;
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


    public function getImage()
    {
            return $this->image;


    }

    public function addImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAudio() === $this) {
                $image->setAudio(null);
            }
        }

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
            $like->setPodcast($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPodcast() === $this) {
                $like->setPodcast(null);
            }
        }

        return $this;
    }
    public function isLikedBy( $user)
    {
        $isLikedBy = false;
        foreach ($this->likes as $like){
            if($like->getAuthor() == $user){
                return true;
            }
        }
        return $isLikedBy;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setAudio($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAudio() === $this) {
                $comment->setAudio(null);
            }
        }

        return $this;
    }

    public function isDisplayComment(): ?bool
    {
        return $this->displayComment;
    }

    public function setDisplayComment(bool $displayComment): static
    {
        $this->displayComment = $displayComment;

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
