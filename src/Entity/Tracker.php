<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TrackerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: [
            'get',
            'delete' => ['security' => 'object.getOwner() == user'],
            'put' // => ['security' => 'object.getOwner() == user'],
        ]
    ),
    ORM\Entity(repositoryClass: TrackerRepository::class)
]
class Tracker
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    protected ?int $id;

    #[ORM\Column(length: 255), Groups('tracker:read')]
    protected ?string $name;

    #[ORM\ManyToOne]
    protected ?Game $game;

    #[ORM\ManyToOne(inversedBy: "trackers"), ORM\JoinColumn(nullable: false)]
    protected ?User $owner;

    #[
        ORM\OneToMany(mappedBy: "tracker", targetEntity: Section::class, orphanRemoval: true),
        ORM\OrderBy(['id' => 'ASC']),
        Groups('tracker:read')
    ]
    protected Collection $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (! $this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setTracker($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        // set the owning side to null (unless already changed)
        if ($this->sections->removeElement($section) && $section->getTracker() === $this) {
            $section->setTracker(null);
        }

        return $this;
    }
}
