<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;

class AddCauseCommandSuccess
{
    protected Counter $cause;

    protected string $channel;

    public function __construct(Counter $cause, string $channel)
    {
        $this->cause = $cause;
        $this->channel = $channel;
    }

    public function getCause(): Counter
    {
        return $this->cause;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}
