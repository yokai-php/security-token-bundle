<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event being dispatched when a Token is fetched but already consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenAlreadyConsumedEvent extends Event
{
    /**
     * @var string
     */
    private $purpose;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $purpose The token purpose
     * @param string $value   The token value
     */
    public function __construct(string $purpose, string $value)
    {
        $this->purpose = $purpose;
        $this->value = $value;
    }

    /**
     * The token purpose
     *
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * The token value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
