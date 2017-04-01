<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

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
     * @param TokenFactoryInterface       $factory
     * @param TokenRepositoryInterface    $repository
     * @param InformationGuesserInterface $informationGuesser
     * @param UserManagerInterface        $userManager
     */
    public function __construct(
        TokenFactoryInterface $factory,
        TokenRepositoryInterface $repository,
        InformationGuesserInterface $informationGuesser,
        UserManagerInterface $userManager
    ) {
        $this->factory = $factory;
        $this->repository = $repository;
        $this->informationGuesser = $informationGuesser;
        $this->userManager = $userManager;
    }

    /**
     * @inheritdoc
     */
    public function get($purpose, $value)
    {
        return $this->repository->get($value, $purpose);
    }

    /**
     * @inheritdoc
     */
    public function create($purpose, $user)
    {
        do {
            $token = $this->factory->create($user, $purpose);
        } while ($this->repository->exists($token->getValue(), $purpose));

        $this->repository->create($token);

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function setUsed(Token $token, DateTime $at = null)
    {
        $token->setUsedAt($at ?: new DateTime());
        $token->setUsedInformation($this->informationGuesser->get());

        $this->repository->update($token);
    }

    /**
     * @inheritdoc
     */
    public function getUser(Token $token)
    {
        return $this->userManager->get($token->getUserClass(), $token->getUserId());
    }
}
