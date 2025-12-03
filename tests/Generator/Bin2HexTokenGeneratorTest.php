<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Generator;

use PHPUnit\Framework\Attributes\DataProvider;
use Yokai\SecurityTokenBundle\Generator\Bin2HexTokenGenerator;
use PHPUnit\Framework\TestCase;

final class Bin2HexTokenGeneratorTest extends TestCase
{
    #[DataProvider('length')]
    public function test_it_generate_unique_token(int $length): void
    {
        $generator = new Bin2HexTokenGenerator($length);

        $tokens = [];
        for ($i = 1; $i <= 1000; $i++) {
            $tokens[] = $value = $generator->generate();
            self::assertSame($length, \mb_strlen($value));
        }

        self::assertSame(\array_unique($tokens), $tokens);
    }

    public static function length(): \Generator
    {
        yield [64];
        yield [10];
        yield [11];
    }
}
