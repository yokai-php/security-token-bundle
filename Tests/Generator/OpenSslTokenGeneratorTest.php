<?php

namespace Yokai\SecurityTokenBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Generator\OpenSslTokenGenerator;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class OpenSslTokenGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_generate_unique_token()
    {
        $generator = new OpenSslTokenGenerator();

        $tokens = [];

        for ($i = 1; $i <= 1000; $i++) {
            $tokens[] = $generator->generate();
        }

        self::assertSame(array_unique($tokens), $tokens);
    }
}
