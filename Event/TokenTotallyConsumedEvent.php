<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched after a Token is totally consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenTotallyConsumedEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token The totally consumed token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * The totally consumed token
     *
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
