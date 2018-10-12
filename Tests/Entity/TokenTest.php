<?php

namespace Yokai\SecurityTokenBundle\Tests\Entity;

use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testLimitedUsagesToken()
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

    public function testUnlimitedUsagesToken()
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
