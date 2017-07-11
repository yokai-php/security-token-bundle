<?php

namespace Yokai\SecurityTokenBundle\Manager;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ChainUserManager implements UserManagerInterface
{
    /**
     * @var UserManagerInterface[]
     */
    private $managers;

    /**
     * @param UserManagerInterface[] $managers
     */
    public function __construct($managers)
    {
        $this->managers = $managers;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        try {
            $manager = $this->getManagerForClass($class);
        } catch (\Exception $exception) {
            return false;
        }

        return $manager instanceof UserManagerInterface;
    }

    /**
     * @inheritDoc
     */
    public function supportsUser($user)
    {
        try {
            $manager = $this->getManagerForUser($user);
        } catch (\Exception $exception) {
            return false;
        }

        return $manager instanceof UserManagerInterface;
    }

    /**
     * @inheritDoc
     */
    public function get($class, $id)
    {
        return $this->getManagerForClass($class)->get($class, $id);
    }

    /**
     * @inheritDoc
     */
    public function getClass($user)
    {
        return $this->getManagerForUser($user)->getClass($user);
    }

    /**
     * @inheritDoc
     */
    public function getId($user)
    {
        return $this->getManagerForUser($user)->getId($user);
    }

    /**
     * @param string $class
     *
     * @return UserManagerInterface
     */
    private function getManagerForClass($class)
    {
        $tries = [];

        foreach ($this->managers as $manager) {
            if ($manager->supportsClass($class)) {
                return $manager;
            }

            $tries[] = get_class($manager);
        }

        throw new \RuntimeException(
            sprintf(
                'Class "%s" is not supported by any UserManager. Tried "%s".',
                $class,
                implode('", "', $tries)
            )
        );
    }

    /**
     * @param mixed $user
     *
     * @return UserManagerInterface
     */
    private function getManagerForUser($user)
    {
        $tries = [];

        foreach ($this->managers as $manager) {
            if ($manager->supportsUser($user)) {
                return $manager;
            }

            $tries[] = get_class($manager);
        }

        throw new \RuntimeException(
            sprintf(
                'User "%s" is not supported by any UserManager. Tried "%s".',
                $user,
                implode('", "', $tries)
            )
        );
    }
}
