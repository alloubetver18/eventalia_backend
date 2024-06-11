<?php

namespace App\Entity;

use App\Repository\CommonUsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommonUsersRepository::class)]
class CommonUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\OneToOne(inversedBy: 'commonUsers', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $User = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'commonUsersFriends')]
    private Collection $friendship;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'friendship')]
    private Collection $commonUsersFriends;

    /**
     * @var Collection<int, Organizations>
     */
    #[ORM\ManyToMany(targetEntity: Organizations::class, inversedBy: 'commonUsersFollowers')]
    private Collection $following;

    public function __construct()
    {
        $this->friendship = new ArrayCollection();
        $this->commonUsersFriends = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->User;
    }

    public function setUser(Users $User): static
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriendship(): Collection
    {
        return $this->friendship;
    }

    public function addFriendship(self $friendship): static
    {
        if (!$this->friendship->contains($friendship)) {
            $this->friendship->add($friendship);
        }

        return $this;
    }

    public function removeFriendship(self $friendship): static
    {
        $this->friendship->removeElement($friendship);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommonUsersFriends(): Collection
    {
        return $this->commonUsersFriends;
    }

    public function addCommonUsersFriend(self $commonUsersFriend): static
    {
        if (!$this->commonUsersFriends->contains($commonUsersFriend)) {
            $this->commonUsersFriends->add($commonUsersFriend);
            $commonUsersFriend->addFriendship($this);
        }

        return $this;
    }

    public function removeCommonUsersFriend(self $commonUsersFriend): static
    {
        if ($this->commonUsersFriends->removeElement($commonUsersFriend)) {
            $commonUsersFriend->removeFriendship($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Organizations>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(Organizations $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
        }

        return $this;
    }

    public function removeFollowing(Organizations $following): static
    {
        $this->following->removeElement($following);

        return $this;
    }
}
