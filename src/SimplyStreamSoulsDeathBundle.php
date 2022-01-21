<?php

namespace SimplyStream\SoulsDeathBundle;

use SimplyStream\SoulsDeathBundle\DependencyInjection\SimplyStreamSoulsDeathExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SimplyStreamSoulsDeathBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new SimplyStreamSoulsDeathExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
