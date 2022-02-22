<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionSuccessEvent extends Event
{
    public const NAME = 'simply-stream.commands.execute.success';

    protected Counter $counter;

    protected UserInterface $user;

    protected string $channel;

    public function __construct(Counter $counter, UserInterface $user, string $channel)
    {
        $this->counter = $counter;
        $this->user = $user;
        $this->channel = $channel;
    }

    public function getCounter(): Counter
    {
        return $this->counter;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}
