<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $createdBy = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'anwsers')]
    private ?self $answers = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'answers')]
    private Collection $anwsers;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Events $eventRelated = null;

    public function __construct()
    {
        $this->anwsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedBy(): ?Users
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Users $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getAnswers(): ?self
    {
        return $this->answers;
    }

    public function setAnswers(?self $answers): static
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAnwsers(): Collection
    {
        return $this->anwsers;
    }

    public function addAnwser(self $anwser): static
    {
        if (!$this->anwsers->contains($anwser)) {
            $this->anwsers->add($anwser);
            $anwser->setAnswers($this);
        }

        return $this;
    }

    public function removeAnwser(self $anwser): static
    {
        if ($this->anwsers->removeElement($anwser)) {
            // set the owning side to null (unless already changed)
            if ($anwser->getAnswers() === $this) {
                $anwser->setAnswers(null);
            }
        }

        return $this;
    }

    public function getEventRelated(): ?Events
    {
        return $this->eventRelated;
    }

    public function setEventRelated(?Events $eventRelated): static
    {
        $this->eventRelated = $eventRelated;

        return $this;
    }
}
