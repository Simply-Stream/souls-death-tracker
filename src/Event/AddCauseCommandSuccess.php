<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;

class AddCauseCommandSuccess
{
    /** @var Counter */
    protected Counter $cause;

    /** @var string */
    protected string $channel;

    /**
     * @param Counter $cause
     * @param string  $channel
     */
    public function __construct(Counter $cause, string $channel)
    {
        $this->cause = $cause;
        $this->channel = $channel;
    }

    /**
     * @return Counter
     */
    public function getCause(): Counter
    {
        return $this->cause;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }
}
