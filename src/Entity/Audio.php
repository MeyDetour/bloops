<?php

namespace App\Entity;

use App\Repository\AudioRepository;
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

    #[ORM\ManyToOne(inversedBy: 'audio')]
    #[ORM\JoinColumn(nullable: true)] // Rend la relation nullable
    private ?Article $article = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
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
}
