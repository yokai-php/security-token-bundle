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
final class TokenRetrievedEvent extends Event
{
    public function __construct(
        /**
         * The consumed token
         */
        public readonly Token $token,
    ) {
    }
}
