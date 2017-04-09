<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
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
     * @param TokenConfigurationRegistry  $registry
     * @param InformationGuesserInterface $informationGuesser
     * @param UserManagerInterface        $userManager
     * @param TokenRepositoryInterface    $repository
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
    public function create($user, $purpose, array $payload = [])
    {
        $configuration = $this->registry->get($purpose);

        do {
            $value = $configuration->getGenerator()->generate();
        } while ($this->repository->exists($value, $purpose));

        return new Token(
            $this->userManager->getClass($user),
            $this->userManager->getId($user),
            $value,
            $purpose,
            $configuration->getDuration(),
            $configuration->getKeep(),
            $configuration->getUsages(),
            $payload,
            $this->informationGuesser->get()
        );
    }
}
