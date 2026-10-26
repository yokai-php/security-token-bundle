<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Manager\TokenManagerInterface;

class DoctrineTokenLifecycleTest extends KernelTestCase
{
    private EntityManagerInterface $manager;
    private TokenManagerInterface $tokens;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->manager = $container->get('doctrine')->getManager();
        $this->tokens = $container->get('yokai_security_token.token_manager');
        (new SchemaTool($this->manager))->createSchema($this->manager->getMetadataFactory()->getAllMetadata());
    }

    private function saveToken(string $purpose, string $duration = '+1 day'): string
    {
        $value = bin2hex(random_bytes(20));
        $token = new Token('App\\Entity\\User', '42', $value, $purpose, $duration, '+1 month', 1, []);
        $this->manager->persist($token);
        $this->manager->flush();
        $this->manager->clear();

        return $value;
    }

    public function testConsumedTokenCannotBeUsedAgainAfterReload(): void
    {
        $value = $this->saveToken('reset_password');
        $token = $this->tokens->get('reset_password', $value);
        self::assertSame($value, $token->getValue());
        self::assertSame('42', $token->getUserId());
        $this->tokens->consume($token);
        $this->manager->clear();
        self::assertSame(1, $this->manager->getRepository(TokenUsage::class)->count([]));
        $this->expectException(TokenConsumedException::class);
        $this->tokens->get('reset_password', $value);
    }

    public function testPurposeCannotBeChangedToReuseAnActivationToken(): void
    {
        $value = $this->saveToken('verify_email');
        self::assertSame('verify_email', $this->tokens->get('verify_email', $value)->getPurpose());
        $this->expectException(TokenNotFoundException::class);
        $this->tokens->get('reset_password', $value);
    }

    public function testExpiredTokenIsRejectedAfterReload(): void
    {
        $value = $this->saveToken('reset_password', '-1 day');
        $this->expectException(TokenExpiredException::class);
        $this->tokens->get('reset_password', $value);
    }
}
