<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

class Counter
{
    /** @var int|null  */
    protected ?int $id;

    /** @var int|null  */
    protected ?int $deaths = 0;

    /** @var string|null  */
    protected ?string $cause;

    /** @var Section|null  */
    protected ?Section $section;

    /** @var string|null  */
    protected ?string $alias;

    /** @var bool  */
    protected bool $successful = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getDeaths(): ?int
    {
        return $this->deaths;
    }

    /**
     * @param int $deaths
     *
     * @return $this
     */
    public function setDeaths(int $deaths): self
    {
        $this->deaths = $deaths;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCause(): ?string
    {
        return $this->cause;
    }

    /**
     * @param string $cause
     *
     * @return $this
     */
    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    /**
     * @return Section|null
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param Section|null $section
     *
     * @return $this
     */
    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string|null $alias
     *
     * @return $this
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * @param bool $successful
     *
     * @return $this
     */
    public function setSuccessful(bool $successful): self
    {
        $this->successful = $successful;

        return $this;
    }
}
