<?php

namespace App\Event;

class TrackerCommandKilledEvent extends TrackerCommandUpdateEvent
{
    public const NAME = 'tracker.killed';
}
