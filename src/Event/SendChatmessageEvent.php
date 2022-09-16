<?php

namespace SimplyStream\SoulsDeathBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SendChatmessageEvent extends Event
{
    /** @var string */
    public const NAME = 'simply-stream.send_chat_message';

    /** @var string */
    protected string $channel;

    /** @var string */
    protected string $chatMessage;

    /**
     * @param string $channel
     * @param string $chatMessage
     */
    public function __construct(string $channel, string $chatMessage)
    {
        $this->channel = $channel;
        $this->chatMessage = $chatMessage;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getChatMessage(): string
    {
        return $this->chatMessage;
    }
}
