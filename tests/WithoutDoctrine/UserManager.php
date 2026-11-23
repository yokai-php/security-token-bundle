<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\WithoutDoctrine;

use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;

final class UserManager implements UserManagerInterface
{
    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function get(string $class, string $id): mixed
    {
        return new User($id);
    }

    public function supportsUser(mixed $user): bool
    {
        return $user instanceof User;
    }

    public function getClass(mixed $user): string
    {
        return User::class;
    }

    public function getId(mixed $user): string
    {
        \assert($user instanceof User);

        return $user->id;
    }
}
