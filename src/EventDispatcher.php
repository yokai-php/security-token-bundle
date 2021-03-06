<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle;

use DateTime;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Event\ConsumeTokenEvent;
use Yokai\SecurityTokenBundle\Event\CreateTokenEvent;
use Yokai\SecurityTokenBundle\Event\TokenAlreadyConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenCreatedEvent;
use Yokai\SecurityTokenBundle\Event\TokenExpiredEvent;
use Yokai\SecurityTokenBundle\Event\TokenNotFoundEvent;
use Yokai\SecurityTokenBundle\Event\TokenRetrievedEvent;
use Yokai\SecurityTokenBundle\Event\TokenTotallyConsumedEvent;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class EventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $purpose
     * @param string $value
     * @param array  $payload
     *
     * @return CreateTokenEvent
     */
    public function createToken(string $purpose, string $value, array $payload): CreateTokenEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new CreateTokenEvent($purpose, $value, $payload),
            TokenEvents::CREATE_TOKEN
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenCreatedEvent
     */
    public function tokenCreated(Token $token): TokenCreatedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenCreatedEvent($token),
            TokenEvents::TOKEN_CREATED
        );

        return $event;
    }

    /**
     * @param Token         $token
     * @param DateTime|null $at
     * @param array         $information
     *
     * @return ConsumeTokenEvent
     */
    public function consumeToken(Token $token, DateTime $at = null, array $information = []): ConsumeTokenEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new ConsumeTokenEvent($token, $at, $information),
            TokenEvents::CONSUME_TOKEN
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenConsumedEvent
     */
    public function tokenConsumed(Token $token): TokenConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenConsumedEvent($token),
            TokenEvents::TOKEN_CONSUMED
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenTotallyConsumedEvent
     */
    public function tokenTotallyConsumed(Token $token): TokenTotallyConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenTotallyConsumedEvent($token),
            TokenEvents::TOKEN_TOTALLY_CONSUMED
        );

        return $event;
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenNotFoundEvent
     */
    public function tokenNotFound(string $purpose, string $value): TokenNotFoundEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenNotFoundEvent($purpose, $value),
            TokenEvents::TOKEN_NOT_FOUND
        );

        return $event;
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenExpiredEvent
     */
    public function tokenExpired(string $purpose, string $value): TokenExpiredEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenExpiredEvent($purpose, $value),
            TokenEvents::TOKEN_EXPIRED
        );

        return $event;
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenAlreadyConsumedEvent
     */
    public function tokenAlreadyConsumed(string $purpose, string $value): TokenAlreadyConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenAlreadyConsumedEvent($purpose, $value),
            TokenEvents::TOKEN_ALREADY_CONSUMED
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenRetrievedEvent
     */
    public function tokenRetrieved(Token $token): TokenRetrievedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenRetrievedEvent($token),
            TokenEvents::TOKEN_RETRIEVED
        );

        return $event;
    }
}
