<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Entity;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class TokenTest extends TestCase
{
    public function test_it_allow_limited_usage_token(): void
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

    public function test_it_allow_unlimited_usage_token(): void
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

    public function test_it_has_no_id_until_stored_by_doctrine(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'reset_password', '+1 day', '+1 month');
        $token->consume([]);

        self::assertNull($token->getId());
        self::assertNull($token->getLastUsage()?->getId());
    }

    public function test_it_cannot_be_consumed_beyond_allowed_usages(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'reset_password', '+1 day', '+1 month', 1);
        $token->consume([]);

        $this->expectException(LogicException::class);

        $token->consume([]);
    }
}
