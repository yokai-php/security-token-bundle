<?php

namespace Yokai\SecurityTokenBundle\Exception;

/**
 * Exception thrown when token is fetched, but already consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenConsumedException extends InvalidTokenException
{
    /**
     * Create an instance of this class.
     *
     * @param string $value   Token value
     * @param string $purpose Token purpose
     * @param int    $usages  Count usages
     *
     * @return TokenConsumedException
     */
    public static function create($value, $purpose, $usages)
    {
        return new self(
            sprintf(
                'The "%s" token with value "%s" was used times "%s".',
                $purpose,
                $value,
                $usages
            )
        );
    }
}
