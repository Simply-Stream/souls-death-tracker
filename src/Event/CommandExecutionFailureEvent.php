<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionFailureEvent extends Event
{
    /** @var string */
    public const NAME = 'simply-stream.commands.execute.failure';

    /** @var Counter */
    protected Counter $counter;

    /** @var UserInterface */
    protected UserInterface $user;

    /** @var string */
    protected string $channel;

    /** @var mixed|null */
    protected mixed $error;

    /**
     * @param Counter       $counter
     * @param UserInterface $user
     * @param string        $channel
     * @param mixed|null    $error
     */
    public function __construct(Counter $counter, UserInterface $user, string $channel, mixed $error = null)
    {
        $this->counter = $counter;
        $this->user = $user;
        $this->channel = $channel;
        $this->error = $error;
    }

    /**
     * @return Counter
     */
    public function getCounter(): Counter
    {
        return $this->counter;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return mixed
     */
    public function getError(): mixed
    {
        return $this->error;
    }
}
