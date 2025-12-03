<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\EventDispatcher;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenManager implements TokenManagerInterface
{
    public function __construct(
        private readonly TokenFactoryInterface $factory,
        private readonly TokenRepositoryInterface $repository,
        private readonly InformationGuesserInterface $informationGuesser,
        private readonly UserManagerInterface $userManager,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function get(string $purpose, string $value): Token
    {
        try {
            $token = $this->repository->get($value, $purpose);
        } catch (TokenNotFoundException $exception) {
            $this->eventDispatcher->tokenNotFound($purpose, $value);

            throw $exception;
        } catch (TokenExpiredException $exception) {
            $this->eventDispatcher->tokenExpired($purpose, $value);

            throw $exception;
        } catch (TokenConsumedException $exception) {
            $this->eventDispatcher->tokenAlreadyConsumed($purpose, $value);

            throw $exception;
        }

        $this->eventDispatcher->tokenRetrieved($token);

        return $token;
    }

    public function create(string $purpose, mixed $user, array $payload = []): Token
    {
        $event = $this->eventDispatcher->createToken($purpose, $user, $payload);

        $token = $this->factory->create($user, $purpose, $event->getPayload());

        $this->repository->create($token);

        $this->eventDispatcher->tokenCreated($token);

        return $token;
    }

    public function consume(Token $token, DateTime|null $at = null): void
    {
        $event = $this->eventDispatcher->consumeToken($token, $at, $this->informationGuesser->get());

        $token->consume($event->information, $at);

        $this->repository->update($token);

        $this->eventDispatcher->tokenConsumed($token);
        if ($token->isConsumed()) {
            $this->eventDispatcher->tokenTotallyConsumed($token);
        }
    }

    public function getUser(Token $token): mixed
    {
        return $this->userManager->get($token->getUserClass(), $token->getUserId());
    }
}
