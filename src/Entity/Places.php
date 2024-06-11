<?php

namespace App\Entity;

use App\Repository\PlacesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlacesRepository::class)]
class Places
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $placename = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $province = null;

    #[ORM\Column(length: 255)]
    private ?string $webpage = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $latitude = null;

    #[ORM\Column(length: 255)]
    private ?string $longitude = null;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'place')]
    private Collection $eventsCelebratedAt;

    public function __construct()
    {
        $this->eventsCelebratedAt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlacename(): ?string
    {
        return $this->placename;
    }

    public function setPlacename(string $placename): static
    {
        $this->placename = $placename;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getWebpage(): ?string
    {
        return $this->webpage;
    }

    public function setWebpage(string $webpage): static
    {
        $this->webpage = $webpage;

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEventsCelebratedAt(): Collection
    {
        return $this->eventsCelebratedAt;
    }

    public function addEventsCelebratedAt(Events $eventsCelebratedAt): static
    {
        if (!$this->eventsCelebratedAt->contains($eventsCelebratedAt)) {
            $this->eventsCelebratedAt->add($eventsCelebratedAt);
            $eventsCelebratedAt->setPlace($this);
        }

        return $this;
    }

    public function removeEventsCelebratedAt(Events $eventsCelebratedAt): static
    {
        if ($this->eventsCelebratedAt->removeElement($eventsCelebratedAt)) {
            // set the owning side to null (unless already changed)
            if ($eventsCelebratedAt->getPlace() === $this) {
                $eventsCelebratedAt->setPlace(null);
            }
        }

        return $this;
    }
}
