<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SendChatmessageEvent extends Event
{
    public const NAME = 'simply-stream.send_chat_message';

    protected string $channel;

    protected string $chatMessage;

    public function __construct(string $channel, string $chatMessage)
    {
        $this->channel = $channel;
        $this->chatMessage = $chatMessage;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getChatMessage(): string
    {
        return $this->chatMessage;
    }
}
