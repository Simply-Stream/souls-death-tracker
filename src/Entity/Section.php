<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

class Section
{
    /** @var int|null  */
    protected ?int $id;

    /** @var ArrayCollection|Collection */
    protected Collection|ArrayCollection $deaths;

    /** @var Tracker|null  */
    protected ?Tracker $tracker;

    /** @var string|null  */
    protected ?string $title;

    public function __construct()
    {
        $this->deaths = new ArrayCollection();
    }

    /**
     * @return int|null
     */
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

    /**
     * @param Counter $death
     *
     * @return $this
     */
    public function addDeath(Counter $death): self
    {
        if (! $this->deaths->contains($death)) {
            $this->deaths[] = $death;
            $death->setSection($this);
        }

        return $this;
    }

    /**
     * @param Counter $death
     *
     * @return $this
     */
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

    /**
     * @return Tracker|null
     */
    public function getTracker(): ?Tracker
    {
        return $this->tracker;
    }

    /**
     * @param Tracker|null $tracker
     *
     * @return $this
     */
    public function setTracker(?Tracker $tracker): self
    {
        $this->tracker = $tracker;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    #[Pure] public function getTotalDeaths(): int
    {
        $total = 0;

        foreach ($this->getDeaths() as $death) {
            $total += $death->getDeaths();
        }

        return $total;
    }
}
