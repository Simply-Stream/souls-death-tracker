<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionFailureEvent extends Event
{
    public const NAME = 'simply-stream.commands.execute.failure';

    protected Counter $counter;

    protected UserInterface $user;

    protected string $channel;

    protected mixed $error;

    public function __construct(Counter $counter, UserInterface $user, string $channel, mixed $error = null)
    {
        $this->counter = $counter;
        $this->user = $user;
        $this->channel = $channel;
        $this->error = $error;
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

    public function getError(): mixed
    {
        return $this->error;
    }
}
