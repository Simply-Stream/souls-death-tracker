<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource, ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    protected ?int $id;

    #[
        ORM\OneToMany(mappedBy: "section", targetEntity: Counter::class, cascade: ['remove', 'persist']),
        ORM\OrderBy(['id' => 'ASC'])
    ]
    protected Collection $deaths;

    #[ORM\ManyToOne(inversedBy: "sections"), ORM\JoinColumn(nullable: false)]
    protected ?Tracker $tracker;

    #[ORM\Column(length: 255), Groups(['tracker:read', 'section:read'])]
    protected ?string $title;

    public function __construct()
    {
        $this->deaths = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Counter[]
     */
    public function getDeaths(): Collection
    {
        return $this->deaths;
    }

    public function addDeath(Counter $death): self
    {
        if (! $this->deaths->contains($death)) {
            $this->deaths[] = $death;
            $death->setSection($this);
        }

        return $this;
    }

    public function removeDeath(Counter $death): self
    {
        if ($this->deaths->removeElement($death)) {
            // set the owning side to null (unless already changed)
            if ($death->getSection() === $this) {
                $death->setSection(null);
            }
        }

        return $this;
    }

    public function getTracker(): ?Tracker
    {
        return $this->tracker;
    }

    public function setTracker(?Tracker $tracker): self
    {
        $this->tracker = $tracker;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    #[Pure] public function getTotalDeaths(): int
    {
        $total = 0;

        foreach ($this->getDeaths() as $death) {
            $total += $death->getDeaths();
        }

        return $total;
    }
}
