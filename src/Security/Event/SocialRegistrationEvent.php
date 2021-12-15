<?php

namespace App\Security\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SocialRegistrationEvent extends Event
{
    protected User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
