<?php

namespace Yokai\SecurityTokenBundle\Exception;

use DateTime;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenUsedException extends InvalidTokenException
{
    /**
     * @param string   $value
     * @param string   $purpose
     * @param DateTime $date
     *
     * @return TokenUsedException
     */
    public static function create($value, $purpose, DateTime $date)
    {
        return new self(
            sprintf(
                'The "%s" token with value "%s" was used at "%s".',
                $purpose,
                $value,
                $date->format(DateTime::ISO8601)
            )
        );
    }
}
