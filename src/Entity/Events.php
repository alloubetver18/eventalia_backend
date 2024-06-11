<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromdate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $todate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startat = null;

    #[ORM\Column(length: 1000)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: Types::BLOB)]
    private $imageEvent;

    #[ORM\Column(length: 255)]
    private ?string $imageEventformat = null;

    #[ORM\ManyToOne(inversedBy: 'eventsCreated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $eventcreatedBy = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'eventsParticipatedIn')]
    private Collection $participatingIn;

    /**
     * @var Collection<int, Themes>
     */
    #[ORM\ManyToMany(targetEntity: Themes::class, inversedBy: 'events')]
    private Collection $eventThemes;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'eventRelated', orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'eventsCelebratedAt')]
    private ?Places $place = null;

    public function __construct()
    {
        $this->participatingIn = new ArrayCollection();
        $this->eventThemes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function getFromdate(): ?\DateTimeInterface
    {
        return $this->fromdate;
    }

    public function setFromdate(\DateTimeInterface $fromdate): static
    {
        $this->fromdate = $fromdate;

        return $this;
    }

    public function getTodate(): ?\DateTimeInterface
    {
        return $this->todate;
    }

    public function setTodate(\DateTimeInterface $todate): static
    {
        $this->todate = $todate;

        return $this;
    }

    public function getStartat(): ?\DateTimeInterface
    {
        return $this->startat;
    }

    public function setStartat(?\DateTimeInterface $startat): static
    {
        $this->startat = $startat;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImageEvent()
    {
        return $this->imageEvent;
    }

    public function setImageEvent($imageEvent): static
    {
        $this->imageEvent = $imageEvent;

        return $this;
    }

    public function getImageEventformat(): ?string
    {
        return $this->imageEventformat;
    }

    public function setImageEventformat(string $imageEventformat): static
    {
        $this->imageEventformat = $imageEventformat;

        return $this;
    }

    public function getEventCreatedBy(): ?Users
    {
        return $this->eventcreatedBy;
    }

    public function setEventCreatedBy(?Users $createdBy): static
    {
        $this->eventcreatedBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getParticipatingIn(): Collection
    {
        return $this->participatingIn;
    }

    public function addParticipatingIn(Users $participatingIn): static
    {
        if (!$this->participatingIn->contains($participatingIn)) {
            $this->participatingIn->add($participatingIn);
        }

        return $this;
    }

    public function removeParticipatingIn(Users $participatingIn): static
    {
        $this->participatingIn->removeElement($participatingIn);

        return $this;
    }

    /**
     * @return Collection<int, Themes>
     */
    public function getEventThemes(): Collection
    {
        return $this->eventThemes;
    }

    public function addEventTheme(Themes $eventTheme): static
    {
        if (!$this->eventThemes->contains($eventTheme)) {
            $this->eventThemes->add($eventTheme);
        }

        return $this;
    }

    public function removeEventTheme(Themes $eventTheme): static
    {
        $this->eventThemes->removeElement($eventTheme);

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
            $comment->setEventRelated($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEventRelated() === $this) {
                $comment->setEventRelated(null);
            }
        }

        return $this;
    }

    public function getPlace(): ?Places
    {
        return $this->place;
    }

    public function setPlace(?Places $place): static
    {
        $this->place = $place;

        return $this;
    }
}
