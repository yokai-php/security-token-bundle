<?php

namespace Yokai\SecurityTokenBundle\Exception;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenConsumedException extends TokenUsedException
{
    /**
     * @param string $value
     * @param string $purpose
     * @param int    $usages
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
