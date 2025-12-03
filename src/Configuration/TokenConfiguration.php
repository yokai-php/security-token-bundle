<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Configuration;

use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenConfiguration
{
    public function __construct(
        public readonly string $purpose,
        public readonly TokenGeneratorInterface $generator,
        public readonly string $duration,
        public readonly int $usages,
        public readonly string $keep,
        public readonly bool $unique,
    ) {
    }
}
