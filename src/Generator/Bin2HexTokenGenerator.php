<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Generator;

/**
 * This token generator is using `bin2hex` along with `random_bytes` to generate random token values.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class Bin2HexTokenGenerator implements TokenGeneratorInterface
{
    public function __construct(
        /**
         * @var int<1, max> The token length
         */
        private readonly int $length = 64,
    ) {
    }

    public function generate(): string
    {
        $byteLength = (int)\ceil($this->length / 2);
        \assert($byteLength >= 1);
        $hex = \bin2hex(\random_bytes($byteLength));

        return \substr($hex, 0, $this->length);
    }
}
