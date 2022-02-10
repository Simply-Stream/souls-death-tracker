<?php

namespace SimplyStream\SoulsDeathBundle\Entity;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getDisplayName(): ?string;
}
