<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Generator;

use LogicException;

/**
 * This token generator is using `openssl` extension to generate random token values.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class OpenSslTokenGenerator implements TokenGeneratorInterface
{
    private const DEFAULT_LENGTH = 32;

    /**
     * @var int
     */
    private $length;

    public function __construct(int $length = self::DEFAULT_LENGTH)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new LogicException('The extension "openssl" is required to use "open ssl" token generator.');
        }

        $this->length = $length;
    }

    public function generate(): string
    {
        return rtrim(strtr(base64_encode((string)openssl_random_pseudo_bytes($this->length)), '+/', '-_'), '=');
    }
}
