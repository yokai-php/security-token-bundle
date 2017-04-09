<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Event\ConsumeTokenEvent;
use Yokai\SecurityTokenBundle\Event\CreateTokenEvent;
use Yokai\SecurityTokenBundle\Event\TokenConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenCreatedEvent;
use Yokai\SecurityTokenBundle\Event\TokenExpiredEvent;
use Yokai\SecurityTokenBundle\Event\TokenNotFoundEvent;
use Yokai\SecurityTokenBundle\Event\TokenRetrievedEvent;
use Yokai\SecurityTokenBundle\Event\TokenTotallyConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenUsedEvent;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Exception\TokenUsedException;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\TokenEvents;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenManager implements TokenManagerInterface
{
    /**
     * @var TokenFactoryInterface
     */
    private $factory;

    /**
     * @var TokenRepositoryInterface
     */
    private $repository;

    /**
     * @var InformationGuesserInterface
     */
    private $informationGuesser;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param TokenFactoryInterface       $factory
     * @param TokenRepositoryInterface    $repository
     * @param InformationGuesserInterface $informationGuesser
     * @param UserManagerInterface        $userManager
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        TokenFactoryInterface $factory,
        TokenRepositoryInterface $repository,
        InformationGuesserInterface $informationGuesser,
        UserManagerInterface $userManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->informationGuesser = $informationGuesser;
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function get($purpose, $value)
    {
        try {
            $token = $this->repository->get($value, $purpose);
        } catch (TokenNotFoundException $exception) {
            $this->eventDispatcher->dispatch(TokenEvents::TOKEN_NOT_FOUND, new TokenNotFoundEvent($purpose, $value));

            throw $exception;
        } catch (TokenExpiredException $exception) {
            $this->eventDispatcher->dispatch(TokenEvents::TOKEN_EXPIRED, new TokenExpiredEvent($purpose, $value));

            throw $exception;
        } catch (TokenUsedException $exception) {
            $this->eventDispatcher->dispatch(TokenEvents::TOKEN_USED, new TokenUsedEvent($purpose, $value));

            throw $exception;
        }

        $this->eventDispatcher->dispatch(TokenEvents::TOKEN_RETRIEVED, new TokenRetrievedEvent($token));

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function create($purpose, $user, array $payload = [])
    {
        $event = new CreateTokenEvent($purpose, $user, $payload);
        $this->eventDispatcher->dispatch(TokenEvents::CREATE_TOKEN, $event);

        $token = $this->factory->create($user, $purpose, $event->getPayload());

        $this->repository->create($token);

        $this->eventDispatcher->dispatch(TokenEvents::TOKEN_CREATED, new TokenCreatedEvent($token));

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function setUsed(Token $token, DateTime $at = null)
    {
        @trigger_error(
            'The '.__METHOD__
            .' method is deprecated since version 2.2 and will be removed in 3.0. Use the consume() method instead.',
            E_USER_DEPRECATED
        );

        $this->consume($token, $at);
    }

    /**
     * @inheritDoc
     */
    public function consume(Token $token, DateTime $at = null)
    {
        $event = new ConsumeTokenEvent($token, $at, $this->informationGuesser->get());
        $this->eventDispatcher->dispatch(TokenEvents::CONSUME_TOKEN, $event);

        $token->consume($event->getInformation(), $at);

        $this->repository->update($token);

        $this->eventDispatcher->dispatch(TokenEvents::TOKEN_CONSUMED, new TokenConsumedEvent($token));
        if ($token->isUsed()) {
            $this->eventDispatcher->dispatch(
                TokenEvents::TOKEN_TOTALLY_CONSUMED,
                new TokenTotallyConsumedEvent($token)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getUser(Token $token)
    {
        return $this->userManager->get($token->getUserClass(), $token->getUserId());
    }
}
