<?php

namespace Yokai\SecurityTokenBundle\Exception;

/**
 * Exception thrown when token is not found.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenNotFoundException extends InvalidTokenException
{
    /**
     * Create an instance of this class.
     *
     * @param string $value   The token value
     * @param string $purpose The token purpose
     *
     * @return TokenNotFoundException
     */
    public static function create($value, $purpose)
    {
        return new self(
            sprintf(
                'The "%s" token with value "%s" was not found.',
                $purpose,
                $value
            )
        );
    }
}
