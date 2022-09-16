<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class Tracker
{
    /** @var int|null */
    protected ?int $id;

    /** @var string|null */
    protected ?string $name;

    /** @var string|null */
    protected ?string $commandName;

    /** @var Game|null */
    protected ?Game $game;

    /** @var UserInterface|null */
    protected ?UserInterface $owner;

    /** @var ArrayCollection|Collection */
    protected Collection|ArrayCollection $sections;

    /** @var string|null */
    protected ?string $publicToken;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * @param string $commandName
     *
     * @return $this
     */
    public function setCommandName(string $commandName): self
    {
        $this->commandName = $commandName;

        return $this;
    }

    /**
     * @return Game|null
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * @param Game|null $game
     *
     * @return $this
     */
    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    /**
     * @param UserInterface|null $owner
     *
     * @return $this
     */
    public function setOwner(?UserInterface $owner): self
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

    /**
     * @param Section $section
     *
     * @return $this
     */
    public function addSection(Section $section): self
    {
        if (! $this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setTracker($this);
        }

        return $this;
    }

    /**
     * @param Section $section
     *
     * @return $this
     */
    public function removeSection(Section $section): self
    {
        // set the owning side to null (unless already changed)
        if ($this->sections->removeElement($section) && $section->getTracker() === $this) {
            $section->setTracker(null);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicToken(): ?string
    {
        return $this->publicToken;
    }

    /**
     * @param string $publicToken
     *
     * @return $this
     */
    public function setPublicToken(string $publicToken): Tracker
    {
        $this->publicToken = $publicToken;

        return $this;
    }
}
