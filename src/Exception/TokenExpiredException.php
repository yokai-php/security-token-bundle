<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Exception;

use DateTimeInterface;

/**
 * Exception thrown when token is fetched, but expired.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenExpiredException extends InvalidTokenException
{
    /**
     * Create an instance of this class.
     *
     * @param string   $value   The token value
     * @param string   $purpose The token purpose
     * @param DateTimeInterface $date    The token expiration date
     */
    public static function create(string $value, string $purpose, DateTimeInterface $date): self
    {
        return new self(
            \sprintf(
                'The "%s" token with value "%s" is expired since "%s".',
                $purpose,
                $value,
                $date->format(DateTimeInterface::ATOM),
            ),
        );
    }
}
