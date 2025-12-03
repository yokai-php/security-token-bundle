<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched after a Token is totally consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenTotallyConsumedEvent extends Event
{
    public function __construct(
        /**
         * The consumed token
         */
        public readonly Token $token,
    ) {
    }
}
