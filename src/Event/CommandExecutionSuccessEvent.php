<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionSuccessEvent extends Event
{
    /** @var string */
    public const NAME = 'simply-stream.commands.execute.success';

    /** @var Counter */
    protected Counter $counter;

    /** @var UserInterface */
    protected UserInterface $user;

    /** @var string */
    protected string $channel;

    /**
     * @param Counter       $counter
     * @param UserInterface $user
     * @param string        $channel
     */
    public function __construct(Counter $counter, UserInterface $user, string $channel)
    {
        $this->counter = $counter;
        $this->user = $user;
        $this->channel = $channel;
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
}
