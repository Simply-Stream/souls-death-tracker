<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class TrackerCommandUpdateEvent extends Event
{
    public const NAME = 'tracker.updated';

    protected array $chatmessage;

    public function __construct(array $chatmessage)
    {
        $this->chatmessage = $chatmessage;
    }

    /**
     * @return array
     */
    public function getChatmessage(): array
    {
        return $this->chatmessage;
    }
}
