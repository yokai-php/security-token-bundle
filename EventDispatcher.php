<?php

namespace Yokai\SecurityTokenBundle;

use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
use Yokai\SecurityTokenBundle\Event\TokenUsedEvent;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class EventDispatcher
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
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
    public function createToken($purpose, $value, array $payload)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::CREATE_TOKEN,
            $event = new CreateTokenEvent($purpose, $value, $payload)
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenCreatedEvent
     */
    public function tokenCreated(Token $token)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_CREATED,
            $event = new TokenCreatedEvent($token)
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
    public function consumeToken(Token $token, DateTime $at = null, array $information = [])
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::CONSUME_TOKEN,
            $event = new ConsumeTokenEvent($token, $at, $information)
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenConsumedEvent
     */
    public function tokenConsumed(Token $token)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_CONSUMED,
            $event = new TokenConsumedEvent($token)
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenTotallyConsumedEvent
     */
    public function tokenTotallyConsumed(Token $token)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_TOTALLY_CONSUMED,
            $event = new TokenTotallyConsumedEvent($token)
        );

        return $event;
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenNotFoundEvent
     */
    public function tokenNotFound($purpose, $value)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_NOT_FOUND,
            $event = new TokenNotFoundEvent($purpose, $value)
        );

        return $event;
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenExpiredEvent
     */
    public function tokenExpired($purpose, $value)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_EXPIRED,
            $event = new TokenExpiredEvent($purpose, $value)
        );

        return $event;
    }

    /**
     * @deprecated since 2.3 to be removed in 3.0. Use tokenAlreadyConsumed instead.
     * @param string $purpose
     * @param string $value
     *
     * @return TokenUsedEvent
     */
    public function tokenUsed($purpose, $value)
    {
        @trigger_error(
            __METHOD__.' is deprecated. Use '.__CLASS__.'::tokenAlreadyConsumed instead.'
        );

        return $this->tokenAlreadyConsumed($purpose, $value);
    }

    /**
     * @param string $purpose
     * @param string $value
     *
     * @return TokenAlreadyConsumedEvent
     */
    public function tokenAlreadyConsumed($purpose, $value)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_ALREADY_CONSUMED,
            $event = new TokenAlreadyConsumedEvent($purpose, $value)
        );

        return $event;
    }

    /**
     * @param Token $token
     *
     * @return TokenRetrievedEvent
     */
    public function tokenRetrieved(Token $token)
    {
        $this->eventDispatcher->dispatch(
            TokenEvents::TOKEN_RETRIEVED,
            $event = new TokenRetrievedEvent($token)
        );

        return $event;
    }
}
