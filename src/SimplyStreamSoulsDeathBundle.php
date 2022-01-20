<?php

namespace SimplyStream\SoulsDeathBundle;

use SimplyStream\SoulsDeathBundle\DependencyInjection\SimplyStreamSoulsDeathExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SimplyStreamSoulsDeathBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new SimplyStreamSoulsDeathExtension();
        }

        return $this->extension;
    }

    public function getPath()
    {
        return \dirname(__DIR__);
    }
}
