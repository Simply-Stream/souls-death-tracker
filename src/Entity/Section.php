<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

class Section
{
    protected ?int $id;

    protected Collection $deaths;

    protected ?Tracker $tracker;

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
