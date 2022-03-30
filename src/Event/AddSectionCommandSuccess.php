<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Section;

class AddSectionCommandSuccess
{
    protected Section $section;

    protected string $channel;

    public function __construct(Section $section, string $channel)
    {
        $this->section = $section;
        $this->channel = $channel;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}
