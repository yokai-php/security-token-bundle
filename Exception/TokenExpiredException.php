<?php

namespace Yokai\SecurityTokenBundle\Exception;

use DateTime;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
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
