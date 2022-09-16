<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * @return string|null
     */
    public function getDisplayName(): ?string;
}
