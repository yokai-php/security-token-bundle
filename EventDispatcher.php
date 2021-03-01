<?php

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

    public function createToken(string $purpose, string $value, array $payload): CreateTokenEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new CreateTokenEvent($purpose, $value, $payload),
            TokenEvents::CREATE_TOKEN
        );

        return $event;
    }

    public function tokenCreated(Token $token): TokenCreatedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenCreatedEvent($token),
            TokenEvents::TOKEN_CREATED
        );

        return $event;
    }

    public function consumeToken(Token $token, DateTime $at = null, array $information = []): ConsumeTokenEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new ConsumeTokenEvent($token, $at, $information),
            TokenEvents::CONSUME_TOKEN
        );

        return $event;
    }

    public function tokenConsumed(Token $token): TokenConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenConsumedEvent($token),
            TokenEvents::TOKEN_CONSUMED
        );

        return $event;
    }

    public function tokenTotallyConsumed(Token $token): TokenTotallyConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenTotallyConsumedEvent($token),
            TokenEvents::TOKEN_TOTALLY_CONSUMED
        );

        return $event;
    }

    public function tokenNotFound(string $purpose, string $value): TokenNotFoundEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenNotFoundEvent($purpose, $value),
            TokenEvents::TOKEN_NOT_FOUND
        );

        return $event;
    }

    public function tokenExpired(string $purpose, string $value): TokenExpiredEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenExpiredEvent($purpose, $value),
            TokenEvents::TOKEN_EXPIRED
        );

        return $event;
    }

    public function tokenAlreadyConsumed(string $purpose, string $value): TokenAlreadyConsumedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenAlreadyConsumedEvent($purpose, $value),
            TokenEvents::TOKEN_ALREADY_CONSUMED
        );

        return $event;
    }

    public function tokenRetrieved(Token $token): TokenRetrievedEvent
    {
        $this->eventDispatcher->dispatch(
            $event = new TokenRetrievedEvent($token),
            TokenEvents::TOKEN_RETRIEVED
        );

        return $event;
    }
}
