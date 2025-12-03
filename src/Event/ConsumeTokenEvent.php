<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use DateTime;
use Symfony\Contracts\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched before a Token is consumed.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class ConsumeTokenEvent extends Event
{
    public function __construct(
        /**
         * The consumed token
         */
        public readonly Token $token,
        /**
         * Date/time at which the token has been consumed
         */
        public readonly DateTime|null $at,
        /**
         * Some context information
         * @var array<string, mixed>
         */
        public readonly array $information,
    ) {
    }
}
