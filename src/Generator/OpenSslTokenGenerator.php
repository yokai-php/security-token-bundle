<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Generator;

use LogicException;

/**
 * This token generator is using `openssl` extension to generate random token values.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class OpenSslTokenGenerator implements TokenGeneratorInterface
{
    public function __construct(
        private readonly int $length = 32,
    ) {
        if (!\function_exists('openssl_random_pseudo_bytes')) {
            throw new LogicException('The extension "openssl" is required to use "open ssl" token generator.');
        }
    }

    public function generate(): string
    {
        return \rtrim(\strtr(\base64_encode(\openssl_random_pseudo_bytes($this->length)), '+/', '-_'), '=');
    }
}
