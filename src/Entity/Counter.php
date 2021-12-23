<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CounterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource, ORM\Entity(repositoryClass: CounterRepository::class)]
class Counter
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    protected $id;

    #[ORM\Column]
    protected int $deaths = 0;

    #[ORM\Column(length: 255)]
    protected ?string $cause;

    #[ORM\ManyToOne(inversedBy: "deaths")]
    protected ?Section $section;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $alias;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeaths(): ?int
    {
        return $this->deaths;
    }

    public function setDeaths(int $deaths): self
    {
        $this->deaths = $deaths;

        return $this;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
