<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
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
     * @param TokenConfigurationRegistry $registry
     * @param InformationGuesserInterface $informationGuesser
     */
    public function __construct(TokenConfigurationRegistry $registry, InformationGuesserInterface $informationGuesser)
    {
        $this->registry = $registry;
        $this->informationGuesser = $informationGuesser;
    }

    /**
     * @inheritdoc
     */
    public function create(UserInterface $user, $purpose)
    {
        $configuration = $this->registry->get($purpose);

        return new Token(
            $user,
            $configuration->getGenerator()->generate(),
            $purpose,
            $configuration->getDuration(),
            $this->informationGuesser->get()
        );
    }
}
