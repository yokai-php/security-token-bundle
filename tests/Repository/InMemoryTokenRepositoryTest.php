<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Repository;

use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;
use Yokai\SecurityTokenBundle\Test\TokenRepositoryTestCase;

final class InMemoryTokenRepositoryTest extends TokenRepositoryTestCase
{
    protected function createRepository(): TokenRepositoryInterface
    {
        return new InMemoryTokenRepository();
    }
}
