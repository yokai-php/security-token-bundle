<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched after a Token is consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenConsumedEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token The consumed token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * The consumed token
     *
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
