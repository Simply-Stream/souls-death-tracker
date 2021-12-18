<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 */
class Section
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Counter::class, mappedBy="section")
     */
    private $deaths;

    /**
     * @ORM\ManyToOne(targetEntity=Tracker::class, inversedBy="sections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tracker;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
        if (!$this->deaths->contains($death)) {
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
