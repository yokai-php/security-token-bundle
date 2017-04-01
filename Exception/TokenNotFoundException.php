<?php

namespace Yokai\SecurityTokenBundle\Exception;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenNotFoundException extends InvalidTokenException
{
    /**
     * @param string $value
     * @param string $purpose
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
