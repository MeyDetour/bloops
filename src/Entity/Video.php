<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[Vich\Uploadable]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Vich\UploadableField(mapping: 'videos', fileNameProperty: 'videoName', size: 'videoSize')]
    private ?File $videoFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $videoName = null;

    #[ORM\Column(nullable: true)]
    private ?int $videoSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: true)] // Rend la relation nullable
    private ?Article $article = null;


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $videoFile
     */
    public function setVideoFile(?File $videoFile = null): void
    {
        $this->videoFile = $videoFile;

        if (null !== $videoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    public function setVideoName(?string $videoName): void
    {
        $this->videoName = $videoName;
    }

    public function getVideoName(): ?string
    {
        return $this->videoName;
    }

    public function setVideoSize(?int $videoSize): void
    {
        $this->videoSize = $videoSize;
    }

    public function getVideoSize(): ?int
    {
        return $this->videoSize;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }


}
