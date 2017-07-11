<?php

namespace Yokai\SecurityTokenBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DoctrineUserManager implements UserManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        try {
            $manager = $this->getManagerFor($class);
        } catch (\Exception $exception) {
            return false;
        }

        return $manager instanceof ObjectManager;
    }

    /**
     * @inheritDoc
     */
    public function supportsUser($user)
    {
        return $this->supportsClass(
            $this->getClass($user)
        );
    }

    /**
     * @inheritDoc
     */
    public function get($class, $id)
    {
        return $this->getManagerFor($class)->find($class, $id);
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
        $class = $this->getClass($user);
        $identifiers = $this->getManagerFor($class)->getClassMetadata($class)->getIdentifierValues($user);

        if (count($identifiers) > 1) {
            throw new \RuntimeException('Entities with composite ids are not supported');
        }

        return (string) reset($identifiers);
    }

    /**
     * @param string $class
     *
     * @return ObjectManager
     */
    private function getManagerFor($class)
    {
        $manager = $this->doctrine->getManagerForClass($class);

        if ($manager === null) {
            throw new \RuntimeException(
                sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. Did you forget to map it?',
                    $class
                )
            );
        }

        return $manager;
    }
}
