<?php

namespace App\Message;

final class TwitchChatCommandMessage
{
    protected array $message;

    public function __construct(array $message)
    {
        $this->message = $message;
    }

    public function getMessage(): array
    {
        return $this->message;
    }
}
