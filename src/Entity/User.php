<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(targetEntity: Bloop::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $articles;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $username = null;

    #[ORM\OneToOne(inversedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $likes;



// User.php (Owning side)
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followers')]
    private Collection $followings;

// User.php (Inverse side)
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followings')]
    private Collection $followers;




    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'requester', orphanRemoval: true)]
    private Collection $friendRequests;

    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'requested', orphanRemoval: true)]
    private Collection $friendRequested;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $imageBackground = null;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $reports;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'targetAuthor', orphanRemoval: true)]
    private Collection $reportTarget;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;



    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->followings = new ArrayCollection();
        $this->friendRequests = new ArrayCollection();
        $this->friendRequested = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->reportTarget = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bloop>
     */
    public function getBloops(): Collection
    {
        return $this->articles;
    }

    public function addBloop(Bloop $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeBloop(Bloop $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

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
            $like->setAuthor($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getAuthor() === $this) {
                $like->setAuthor(null);
            }
        }

        return $this;
    }





    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setFollowers($this);
        }

        return $this;
    }

    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFollowers() === $this) {
                $user->setFollowers(null);
            }
        }

        return $this;
    }

    public function getFollowers(): ArrayCollection|Collection
    {
        return $this->followers;
    }

    public function setFollowers(?ArrayCollection $followers): static
    {
        $this->followers = $followers;

        return $this;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->setFollowers($this);
        }

        return $this;
    }

    public function removeFollower(self $follower): static
    {
        if ($this->followers->removeElement($follower)) {
            // set the owning side to null (unless already changed)
            if ($follower->getFollowers() === $this) {
                $follower->setFollowers(null);
            }
        }

        return $this;
    }

    public function getFollowing(): Collection
    {
        return $this->followings;
    }

    public function setFollowing(?ArrayCollection $followings): static
    {
        $this->followings = $followings;

        return $this;
    }

    public function addFollowing(self $followings): static
    {
        if (!$this->followings->contains($followings)) {
            $this->followings->add($followings);
            $followings->setFollowing($this);
        }

        return $this;
    }

    public function removeFollowing(self $followings): static
    {
        if ($this->followings->removeElement($followings)) {
            // set the owning side to null (unless already changed)
            if ($followings->getFollowing() === $this) {
                $followings->setFollowing(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getFriendRequests(): Collection
    {
        return $this->friendRequests;
    }

    public function addFriendRequest(FriendRequest $friendRequest): static
    {
        if (!$this->friendRequests->contains($friendRequest)) {
            $this->friendRequests->add($friendRequest);
            $friendRequest->setRequester($this);
        }

        return $this;
    }

    public function removeFriendRequest(FriendRequest $friendRequest): static
    {
        if ($this->friendRequests->removeElement($friendRequest)) {
            // set the owning side to null (unless already changed)
            if ($friendRequest->getRequester() === $this) {
                $friendRequest->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getFriendRequested(): Collection
    {
        return $this->friendRequested;
    }

    public function addFriendRequested(FriendRequest $friendRequested): static
    {
        if (!$this->friendRequested->contains($friendRequested)) {
            $this->friendRequested->add($friendRequested);
            $friendRequested->setRequested($this);
        }

        return $this;
    }

    public function removeFriendRequested(FriendRequest $friendRequested): static
    {
        if ($this->friendRequested->removeElement($friendRequested)) {
            // set the owning side to null (unless already changed)
            if ($friendRequested->getRequested() === $this) {
                $friendRequested->setRequested(null);
            }
        }

        return $this;
    }

    public function getImageBackground(): ?Image
    {
        return $this->imageBackground;
    }

    public function setImageBackground(?Image $imageBackground): static
    {
        $this->imageBackground = $imageBackground;

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
            $report->setAuthor($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getAuthor() === $this) {
                $report->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReportTarget(): Collection
    {
        return $this->reportTarget;
    }

    public function addReportTarget(Report $reportTarget): static
    {
        if (!$this->reportTarget->contains($reportTarget)) {
            $this->reportTarget->add($reportTarget);
            $reportTarget->setTargetAuthor($this);
        }

        return $this;
    }

    public function removeReportTarget(Report $reportTarget): static
    {
        if ($this->reportTarget->removeElement($reportTarget)) {
            // set the owning side to null (unless already changed)
            if ($reportTarget->getTargetAuthor() === $this) {
                $reportTarget->setTargetAuthor(null);
            }
        }

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
