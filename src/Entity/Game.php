<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

class Game
{
    protected ?int $id;

    protected ?string $name;

    protected array $template;

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

    public function getTemplate(): ?array
    {
        return $this->template;
    }

    public function setTemplate(array $template): self
    {
        $this->template = $template;

        return $this;
    }
}
