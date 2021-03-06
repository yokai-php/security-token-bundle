<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_allow_limited_usage_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique-token', 'reset-password', '+1 day', '+1 month', 2);
        self::assertFalse($token->isConsumed());

        $token->consume([1]);
        self::assertFalse($token->isConsumed());
        $token->consume([2]);
        self::assertTrue($token->isConsumed());

        self::assertCount(2, $token->getUsages());
        self::assertSame(2, $token->getCountUsages());
        self::assertSame([2], $token->getLastUsage()->getInformation());
    }

    /**
     * @test
     */
    public function it_allow_unlimited_usage_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique-token', 'reset-password', '+1 day', '+1 month', 0);
        self::assertFalse($token->isConsumed());

        $token->consume([1]);
        self::assertFalse($token->isConsumed());
        $token->consume([2]);
        self::assertFalse($token->isConsumed());
        $token->consume([3]);
        self::assertFalse($token->isConsumed());

        self::assertCount(3, $token->getUsages());
        self::assertSame(3, $token->getCountUsages());
        self::assertSame([3], $token->getLastUsage()->getInformation());
    }
}
