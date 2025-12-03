<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Manager;

/**
 * Chained user manager, delegate to other user managers.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class ChainUserManager implements UserManagerInterface
{
    public function __construct(
        /**
         * @var iterable<UserManagerInterface> $managers A list of user managers
         */
        private readonly iterable $managers,
    ) {
    }

    public function supportsClass(string $class): bool
    {
        try {
            $this->getManagerForClass($class);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function supportsUser(mixed $user): bool
    {
        try {
            $this->getManagerForUser($user);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function get(string $class, string $id)
    {
        return $this->getManagerForClass($class)->get($class, $id);
    }

    public function getClass(mixed $user): string
    {
        return $this->getManagerForUser($user)->getClass($user);
    }

    public function getId(mixed $user): string
    {
        return $this->getManagerForUser($user)->getId($user);
    }

    /**
     * Find appropriate user manager for a class.
     *
     * @param class-string $class The user class
     *
     * @throws \InvalidArgumentException
     */
    private function getManagerForClass(string $class): UserManagerInterface
    {
        $tries = [];

        foreach ($this->managers as $manager) {
            if ($manager->supportsClass($class)) {
                return $manager;
            }

            $tries[] = \get_class($manager);
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Class "%s" is not supported by any UserManager. Tried "%s".',
                $class,
                \implode('", "', $tries),
            ),
        );
    }

    /**
     * Find appropriate user manager for user.
     *
     * @param mixed $user A user
     *
     * @throws \InvalidArgumentException
     */
    private function getManagerForUser(mixed $user): UserManagerInterface
    {
        $tries = [];

        foreach ($this->managers as $manager) {
            if ($manager->supportsUser($user)) {
                return $manager;
            }

            $tries[] = \get_class($manager);
        }

        if (\is_object($user)) {
            if (!\method_exists($user, '__toString')) {
                $userAsString = \sprintf('%s::%s', \get_class($user), \spl_object_hash($user));
            } else {
                $userAsString = (string)$user;
            }
        } else {
            if (\is_scalar($user)) {
                $userAsString = (string)$user;
            } else {
                $userAsString = \get_debug_type($user);
            }
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'User "%s" is not supported by any UserManager. Tried "%s".',
                $userAsString,
                \implode('", "', $tries),
            ),
        );
    }
}
