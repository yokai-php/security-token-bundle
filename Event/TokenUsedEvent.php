<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @deprecated since 2.3 to be removed in 3.0. Rely on TokenAlreadyConsumedEvent instead.
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenUsedEvent extends Event
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
    public function __construct($purpose, $value)
    {
        $this->purpose = $purpose;
        $this->value = $value;
    }

    /**
     * The token purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * The token value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
