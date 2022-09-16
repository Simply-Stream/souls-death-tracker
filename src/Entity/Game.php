<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

class Game
{
    /** @var int|null */
    protected ?int $id;

    /** @var string|null */
    protected ?string $name;

    /** @var array */
    protected array $template;

    /** @return int|null */
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
     * @return array|null
     */
    public function getTemplate(): ?array
    {
        return $this->template;
    }

    /**
     * @param array $template
     *
     * @return $this
     */
    public function setTemplate(array $template): self
    {
        $this->template = $template;

        return $this;
    }
}
