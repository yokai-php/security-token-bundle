<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event being dispatched when a Token is fetch but expired.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenExpiredEvent extends Event
{
    public function __construct(
        /**
         * The token purpose
         */
        public readonly string $purpose,
        /**
         * The token value
         */
        public readonly string $value,
    ) {
    }
}
