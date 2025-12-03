<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event being dispatched before a Token is created.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class CreateTokenEvent extends Event
{
    public function __construct(
        /**
         * The token purpose
         */
        public readonly string $purpose,
        /**
         * The associated user
         */
        public readonly mixed $user,
        /**
         * The token payload
         * @var array<string, mixed>
         */
        private array $payload,
    ) {
    }

    /**
     * The token payload
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Replace token payload
     *
     * @param array<string, mixed> $payload The new payload value
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Add payload information to token
     *
     * @param array<string, mixed> $payload Some payload to add
     */
    public function addPayload(array $payload): void
    {
        $this->payload = \array_merge($this->payload, $payload);
    }
}
