<?php

namespace Yokai\SecurityTokenBundle\Generator;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class OpenSslTokenGenerator implements TokenGeneratorInterface
{
    const DEFAULT_LENGTH = 32;

    /**
     * @var int
     */
    private $length;

    /**
     * @param int $length
     */
    public function __construct($length = self::DEFAULT_LENGTH)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new \RuntimeException;//todo
        }

        $this->length = $length;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return rtrim(strtr(base64_encode(openssl_random_pseudo_bytes($this->length)), '+/', '-_'), '=');
    }
}
