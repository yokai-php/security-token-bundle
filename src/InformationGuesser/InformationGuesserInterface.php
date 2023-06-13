<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\InformationGuesser;

/**
 * An information guesser is responsible for finding information about the execution context.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface InformationGuesserInterface
{
    /**
     * Get information about the execution context.
     *
     * @return array<string, mixed>
     */
    public function get(): array;
}
