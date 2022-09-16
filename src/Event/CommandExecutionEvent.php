<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionEvent extends Event
{
    /** @var string */
    public const NAME = 'simply-stream.commands.execute';

    /** @var string */
    protected string $command;

    /** @var array */
    protected array $parameters;

    /** @var string */
    protected string $channel;

    /** @var array */
    protected array $chatMessage;

    /**
     * @param string $command
     * @param array  $parameters
     * @param string $channel
     * @param array  $chatMessage
     */
    public function __construct(string $command, array $parameters, string $channel, array $chatMessage)
    {
        $this->command = $command;
        $this->parameters = $parameters;
        $this->channel = $channel;
        $this->chatMessage = $chatMessage;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return array
     */
    public function getChatMessage(): array
    {
        return $this->chatMessage;
    }
}
