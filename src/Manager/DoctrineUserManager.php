<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Manager;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * User manager for doctrine entities.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DoctrineUserManager implements UserManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @param ManagerRegistry $doctrine The doctrine registry
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function supportsClass(string $class): bool
    {
        try {
            $this->getManagerFor($class);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function supportsUser($user): bool
    {
        return $this->supportsClass(
            $this->getClass($user)
        );
    }

    public function get(string $class, string $id)
    {
        return $this->getManagerFor($class)->find($class, $id);
    }

    public function getClass($user): string
    {
        /** @var object $user */
        /** @var class-string $class */
        $class = ClassUtils::getClass($user);

        return $class;
    }

    public function getId($user): string
    {
        /** @var object $user */
        /** @var class-string $class */
        $class = $this->getClass($user);
        $identifiers = $this->getManagerFor($class)->getClassMetadata($class)->getIdentifierValues($user);

        if (count($identifiers) > 1) {
            throw new \InvalidArgumentException('Entities with composite ids are not supported');
        }

        return (string) reset($identifiers);
    }

    /**
     * Get doctrine object manager for a class.
     *
     * @param class-string $class The user class
     */
    private function getManagerFor(string $class): ObjectManager
    {
        $manager = $this->doctrine->getManagerForClass($class);

        if ($manager === null) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. Did you forget to map it?',
                    $class
                )
            );
        }

        return $manager;
    }
}
