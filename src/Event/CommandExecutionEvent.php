<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CommandExecutionEvent extends Event
{
    public const NAME = 'simply-stream.commands.execute';

    protected string $command;

    protected array $parameters;

    protected string $channel;

    protected array $chatMessage;

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
