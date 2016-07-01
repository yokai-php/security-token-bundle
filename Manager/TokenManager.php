<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenManager implements TokenManagerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

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
     * @param TokenStorageInterface       $tokenStorage
     * @param TokenFactoryInterface       $factory
     * @param TokenRepositoryInterface    $repository
     * @param InformationGuesserInterface $informationGuesser
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        TokenFactoryInterface $factory,
        TokenRepositoryInterface $repository,
        InformationGuesserInterface $informationGuesser
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->factory = $factory;
        $this->repository = $repository;
        $this->informationGuesser = $informationGuesser;
    }

    /**
     * @inheritdoc
     */
    public function create($purpose, UserInterface $user = null)
    {
        $user = $this->getUser($user);

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
     * @param UserInterface|null $user
     *
     * @return UserInterface
     */
    private function getUser(UserInterface $user = null)
    {
        if ($user instanceof UserInterface) {
            return $user;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            throw new \RuntimeException();//todo
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new \RuntimeException();//todo
        }

        return $user;
    }
}
