<?php

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenRetrievedEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}
