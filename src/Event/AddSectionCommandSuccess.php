<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Section;

class AddSectionCommandSuccess
{
    /** @var Section  */
    protected Section $section;

    /** @var string  */
    protected string $channel;

    /**
     * @param Section $section
     * @param string  $channel
     */
    public function __construct(Section $section, string $channel)
    {
        $this->section = $section;
        $this->channel = $channel;
    }

    /**
     * @return Section
     */
    public function getSection(): Section
    {
        return $this->section;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }
}
