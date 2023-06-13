<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched after a Token is created.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenCreatedEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token The created token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * The created token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}
