<?php

namespace Yokai\SecurityTokenBundle\Exception;

use DateTime;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenExpiredException extends InvalidTokenException
{
    /**
     * @param string   $value
     * @param string   $purpose
     * @param DateTime $date
     *
     * @return TokenExpiredException
     */
    public static function TokenExpiredException($value, $purpose, DateTime $date)
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
