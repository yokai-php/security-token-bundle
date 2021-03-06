<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched before a Token is fetched.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenRetrievedEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token The retrieved token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * The retrieved token
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}
