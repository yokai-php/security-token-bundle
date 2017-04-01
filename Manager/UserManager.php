<?php

namespace Yokai\SecurityTokenBundle\Manager;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class UserManager implements UserManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function get($class, $id)
    {
        return $this->entityManager->find($class, $id);
    }

    /**
     * @inheritDoc
     */
    public function getClass($user)
    {
        return ClassUtils::getClass($user);
    }

    /**
     * @inheritDoc
     */
    public function getId($user)
    {
        $identifiers = $this->entityManager->getClassMetadata(ClassUtils::getClass($user))->getIdentifierValues($user);

        if (count($identifiers) > 1) {
            throw new \RuntimeException('Entities with composite ids are not supported');
        }

        return (string) reset($identifiers);
    }
}
