<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nick = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $rol = null;

    #[ORM\Column(type: Types::BLOB)]
    private $avatarimage;

    #[ORM\Column(length: 255)]
    private ?string $avatarimageformat = null;

    #[ORM\OneToOne(mappedBy: 'User', cascade: ['persist', 'remove'])]
    private ?CommonUsers $commonUsers = null;

    #[ORM\OneToOne(mappedBy: 'User', cascade: ['persist', 'remove'])]
    private ?Organizations $organizations = null;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'eventcreatedBy', orphanRemoval: true)]
    private Collection $eventsCreated;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\ManyToMany(targetEntity: Events::class, mappedBy: 'participatingIn')]
    private Collection $eventsParticipatedIn;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'createdBy', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Themes>
     */
    #[ORM\ManyToMany(targetEntity: Themes::class, inversedBy: 'users')]
    private Collection $themesInterested;

    public function __construct()
    {
        $this->eventsCreated = new ArrayCollection();
        $this->eventsParticipatedIn = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->themesInterested = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
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

    public function getRol(): ?int
    {
        return $this->rol;
    }

    public function setRol(int $rol): static
    {
        $this->rol = $rol;

        return $this;
    }

    public function getAvatarimage()
    {
        return $this->avatarimage;
    }

    public function setAvatarimage($avatarimage): static
    {
        $this->avatarimage = $avatarimage;

        return $this;
    }

    public function getAvatarimageformat(): ?string
    {
        return $this->avatarimageformat;
    }

    public function setAvatarimageformat(string $avatarimageformat): static
    {
        $this->avatarimageformat = $avatarimageformat;

        return $this;
    }

    public function getCommonUsers(): ?CommonUsers
    {
        return $this->commonUsers;
    }

    public function setCommonUsers(CommonUsers $commonUsers): static
    {
        // set the owning side of the relation if necessary
        if ($commonUsers->getUser() !== $this) {
            $commonUsers->setUser($this);
        }

        $this->commonUsers = $commonUsers;

        return $this;
    }

    public function getOrganizations(): ?Organizations
    {
        return $this->organizations;
    }

    public function setOrganizations(Organizations $organizations): static
    {
        // set the owning side of the relation if necessary
        if ($organizations->getUser() !== $this) {
            $organizations->setUser($this);
        }

        $this->organizations = $organizations;

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEventsCreated(): Collection
    {
        return $this->eventsCreated;
    }

    public function addEventsCreated(Events $eventsCreated): static
    {
        if (!$this->eventsCreated->contains($eventsCreated)) {
            $this->eventsCreated->add($eventsCreated);
            $eventsCreated->seteventCreatedBy($this);
        }

        return $this;
    }

    public function removeEventsCreated(Events $eventsCreated): static
    {
        if ($this->eventsCreated->removeElement($eventsCreated)) {
            // set the owning side to null (unless already changed)
            if ($eventsCreated->geteventCreatedBy() === $this) {
                $eventsCreated->seteventCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEventsParticipatedIn(): Collection
    {
        return $this->eventsParticipatedIn;
    }

    public function addEventsParticipatedIn(Events $eventsParticipatedIn): static
    {
        if (!$this->eventsParticipatedIn->contains($eventsParticipatedIn)) {
            $this->eventsParticipatedIn->add($eventsParticipatedIn);
            $eventsParticipatedIn->addParticipatingIn($this);
        }

        return $this;
    }

    public function removeEventsParticipatedIn(Events $eventsParticipatedIn): static
    {
        if ($this->eventsParticipatedIn->removeElement($eventsParticipatedIn)) {
            $eventsParticipatedIn->removeParticipatingIn($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCreatedBy($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCreatedBy() === $this) {
                $comment->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Themes>
     */
    public function getThemesInterested(): Collection
    {
        return $this->themesInterested;
    }

    public function addThemesInterested(Themes $themesInterested): static
    {
        if (!$this->themesInterested->contains($themesInterested)) {
            $this->themesInterested->add($themesInterested);
        }

        return $this;
    }

    public function removeThemesInterested(Themes $themesInterested): static
    {
        $this->themesInterested->removeElement($themesInterested);

        return $this;
    }
}
