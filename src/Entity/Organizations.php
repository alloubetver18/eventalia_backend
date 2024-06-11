<?php

namespace App\Entity;

use App\Repository\OrganizationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationsRepository::class)]
class Organizations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $organizationName = null;

    #[ORM\Column(length: 500)]
    private ?string $organizationDescription = null;

    #[ORM\Column(length: 50)]
    private ?string $organizationWebpage = null;

    #[ORM\OneToOne(inversedBy: 'organizations', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $User = null;

    /**
     * @var Collection<int, CommonUsers>
     */
    #[ORM\ManyToMany(targetEntity: CommonUsers::class, mappedBy: 'following')]
    private Collection $commonUsersFollowers;

    public function __construct()
    {
        $this->commonUsersFollowers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(string $organizationName): static
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    public function getOrganizationDescription(): ?string
    {
        return $this->organizationDescription;
    }

    public function setOrganizationDescription(string $organizationDescription): static
    {
        $this->organizationDescription = $organizationDescription;

        return $this;
    }

    public function getOrganizationWebpage(): ?string
    {
        return $this->organizationWebpage;
    }

    public function setOrganizationWebpage(string $organizationWebpage): static
    {
        $this->organizationWebpage = $organizationWebpage;

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
     * @return Collection<int, CommonUsers>
     */
    public function getCommonUsersFollowers(): Collection
    {
        return $this->commonUsersFollowers;
    }

    public function addCommonUsersFollower(CommonUsers $commonUsersFollower): static
    {
        if (!$this->commonUsersFollowers->contains($commonUsersFollower)) {
            $this->commonUsersFollowers->add($commonUsersFollower);
            $commonUsersFollower->addFollowing($this);
        }

        return $this;
    }

    public function removeCommonUsersFollower(CommonUsers $commonUsersFollower): static
    {
        if ($this->commonUsersFollowers->removeElement($commonUsersFollower)) {
            $commonUsersFollower->removeFollowing($this);
        }

        return $this;
    }
}
