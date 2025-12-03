<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Generator\OpenSslTokenGenerator;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class OpenSslTokenGeneratorTest extends TestCase
{
    public function test_it_generate_unique_token(): void
    {
        $generator = new OpenSslTokenGenerator();

        $tokens = [];

        for ($i = 1; $i <= 1000; $i++) {
            $tokens[] = $generator->generate();
        }

        self::assertSame(\array_unique($tokens), $tokens);
    }
}
