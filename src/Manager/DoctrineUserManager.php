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
final class DoctrineUserManager implements UserManagerInterface
{
    public function __construct(
        /**
         * @var ManagerRegistry $doctrine The doctrine registry
         */
        private readonly ManagerRegistry $doctrine,
    ) {
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

    public function supportsUser(mixed $user): bool
    {
        return $this->supportsClass(
            $this->getClass($user),
        );
    }

    public function get(string $class, string $id)
    {
        return $this->getManagerFor($class)->find($class, $id);
    }

    public function getClass(mixed $user): string
    {
        /** @var object $user */

        return ClassUtils::getClass($user);
    }

    public function getId(mixed $user): string
    {
        /** @var object $user */
        /** @var class-string $class */
        $class = $this->getClass($user);
        $identifiers = $this->getManagerFor($class)->getClassMetadata($class)->getIdentifierValues($user);

        if (\count($identifiers) > 1) {
            throw new \InvalidArgumentException('Entities with composite ids are not supported');
        }

        $identifier = \reset($identifiers);
        if (\is_scalar($identifier)
            || $identifier === null
            || (\is_object($identifier) && \method_exists($identifier, '__toString'))
        ) {
            return (string)$identifier;
        }

        throw new \InvalidArgumentException('Entities with non stringable ids are not supported');
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
                \sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. Did you forget to map it?',
                    $class,
                ),
            );
        }

        return $manager;
    }
}
