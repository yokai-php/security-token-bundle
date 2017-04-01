<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;

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
     * @param TokenConfigurationRegistry  $registry
     * @param InformationGuesserInterface $informationGuesser
     * @param UserManagerInterface        $userManager
     */
    public function __construct(
        TokenConfigurationRegistry $registry,
        InformationGuesserInterface $informationGuesser,
        UserManagerInterface $userManager
    ) {
        $this->registry = $registry;
        $this->informationGuesser = $informationGuesser;
        $this->userManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function create($user, $purpose)
    {
        $configuration = $this->registry->get($purpose);

        return new Token(
            $this->userManager->getClass($user),
            $this->userManager->getId($user),
            $configuration->getGenerator()->generate(),
            $purpose,
            $configuration->getDuration(),
            $this->informationGuesser->get()
        );
    }
}
