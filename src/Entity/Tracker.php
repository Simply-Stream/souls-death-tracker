<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class Tracker
{
    protected ?int $id;

    protected ?string $name;

    private $commandName;

    protected ?Game $game;

    protected ?UserInterface $owner;

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

    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    public function setCommandName(string $commandName): self
    {
        $this->commandName = $commandName;

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
