<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\WithoutDoctrine;

final class User
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
