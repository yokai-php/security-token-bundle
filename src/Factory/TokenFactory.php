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
class TokenFactory implements TokenFactoryInterface
{
    /**
     * @var TokenConfigurationRegistry
     */
    private $registry;

    /**
     * @var InformationGuesserInterface
     */
    private $informationGuesser;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var TokenRepositoryInterface
     */
    private $repository;

    /**
     * @param TokenConfigurationRegistry  $registry           The configuration registry
     * @param InformationGuesserInterface $informationGuesser The information guesser
     * @param UserManagerInterface        $userManager        The user manager
     * @param TokenRepositoryInterface    $repository         The token repository
     */
    public function __construct(
        TokenConfigurationRegistry $registry,
        InformationGuesserInterface $informationGuesser,
        UserManagerInterface $userManager,
        TokenRepositoryInterface $repository
    ) {
        $this->registry = $registry;
        $this->informationGuesser = $informationGuesser;
        $this->userManager = $userManager;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function create($user, string $purpose, array $payload = []): Token
    {
        // get configuration for this token purpose
        $configuration = $this->registry->get($purpose);

        // extract user information
        $userClass = $this->userManager->getClass($user);
        $userId = $this->userManager->getId($user);

        // if configuration for this token tells that it can only exists one Token for this user
        if ($configuration->isUnique()) {
            $token = $this->repository->findExisting($userClass, $userId, $purpose);

            // a token already exists for this user and this purpose, return it
            if ($token instanceof Token) {
                return $token;
            }
        }

        // enforce token uniqueness
        // generate a value while it exists already
        do {
            $value = $configuration->getGenerator()->generate();
        } while ($this->repository->exists($value, $purpose));

        // extract configuration values
        $duration = $configuration->getDuration();
        $keep = $configuration->getKeep();
        $usages = $configuration->getUsages();

        // extract context information
        $information = $this->informationGuesser->get();

        return new Token($userClass, $userId, $value, $purpose, $duration, $keep, $usages, $payload, $information);
    }
}
