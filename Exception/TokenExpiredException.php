<?php

namespace Yokai\SecurityTokenBundle\Exception;

use DateTime;

/**
 * Exception thrown when token is fetched, but expired.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenExpiredException extends InvalidTokenException
{
    /**
     * Create an instance of this class.
     *
     * @param string   $value   The token value
     * @param string   $purpose The token purpose
     * @param DateTime $date    The token expiration date
     *
     * @return TokenExpiredException
     */
    public static function create($value, $purpose, DateTime $date)
    {
        return new self(
            sprintf(
                'The "%s" token with value "%s" is expired since "%s".',
                $purpose,
                $value,
                $date->format(DateTime::ISO8601)
            )
        );
    }

}
