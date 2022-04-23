<?php

namespace SimplyStream\SoulsDeathBundle\Service;

class TokenGenerator
{
    /**
     * Generates a token of $length
     *
     * @param int $length
     *
     * @return string
     * @throws \Exception
     */
    public function generate(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
