<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\WithoutDoctrine;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;

final class WithoutDoctrineTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    public function testTokenLifecycleWorksWithACustomStorage(): void
    {
        $container = self::bootKernel()->getContainer();
        $tokens = $container->get('yokai_security_token.token_manager');

        $created = $tokens->create('reset_password', new User('42'));
        $token = $tokens->get('reset_password', $created->getValue());
        self::assertEquals(new User('42'), $tokens->getUser($token));

        $tokens->consume($token);
        $this->expectException(TokenConsumedException::class);
        $tokens->get('reset_password', $created->getValue());
    }

    public function testArchivingWorksWithACustomStorage(): void
    {
        $container = self::bootKernel()->getContainer();
        $container->get('yokai_security_token.token_manager')->create('reset_password', new User('42'));

        // tokens are kept for a month by default: nothing to archive yet
        self::assertSame(0, $container->get('yokai_security_token.archivist')->archive());
        self::assertCount(1, $container->get(InMemoryTokenRepository::class)->all());
    }
}
