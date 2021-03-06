<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Generator;

/**
 * A token generator is responsible for generating a random, unreadable, unique (as unique as possible) value.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenGeneratorInterface
{
    /**
     * The generated token string.
     *
     * @return string
     */
    public function generate(): string;
}
