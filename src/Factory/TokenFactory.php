<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Factory;

use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * Uses configuration to determine Token creation rules.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class TokenFactory implements TokenFactoryInterface
{
    public function __construct(
        private readonly TokenConfigurationRegistry $registry,
        private readonly InformationGuesserInterface $informationGuesser,
        private readonly UserManagerInterface $userManager,
        private readonly TokenRepositoryInterface $repository,
    ) {
    }

    public function create(mixed $user, string $purpose, array $payload = []): Token
    {
        // get configuration for this token purpose
        $configuration = $this->registry->get($purpose);

        // extract user information
        $userClass = $this->userManager->getClass($user);
        $userId = $this->userManager->getId($user);

        // if configuration for this token tells that it can only exists one Token for this user
        if ($configuration->unique) {
            $token = $this->repository->findExisting($userClass, $userId, $purpose);

            // a token already exists for this user and this purpose, return it
            if ($token instanceof Token) {
                return $token;
            }
        }

        // enforce token uniqueness
        // generate a value while it exists already
        do {
            $value = $configuration->generator->generate();
        } while ($this->repository->exists($value, $purpose));

        return new Token(
            userClass: $userClass,
            userId: $userId,
            value: $value,
            purpose: $purpose,
            validDuration: $configuration->duration,
            keepDuration: $configuration->keep,
            allowedUsages: $configuration->usages,
            payload: $payload,
            information: $this->informationGuesser->get(),
        );
    }
}
