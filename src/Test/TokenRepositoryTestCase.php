<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * Behavior every token storage must have.
 * Extend this test case to check your own implementation of TokenRepositoryInterface.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
abstract class TokenRepositoryTestCase extends TestCase
{
    /**
     * Create the token repository under test, with an empty storage.
     */
    abstract protected function createRepository(): TokenRepositoryInterface;

    /**
     * Forget whatever the storage keeps in memory, so that next reads hit the storage.
     * Override it if your storage has a cache, like the Doctrine identity map.
     */
    protected function refresh(): void
    {
    }

    public function testGetThrowsWhenTokenDoesNotExist(): void
    {
        $this->expectException(TokenNotFoundException::class);

        $this->createRepository()->get('unknown', 'reset_password');
    }

    public function testGetReturnsCreatedToken(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('value', 'reset_password'));
        $this->refresh();

        $token = $repository->get('value', 'reset_password');

        self::assertSame('value', $token->getValue());
        self::assertSame('reset_password', $token->getPurpose());
        self::assertSame('42', $token->getUserId());
    }

    public function testGetThrowsWhenTokenIsExpired(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('value', 'reset_password', duration: '-1 day'));
        $this->refresh();

        $this->expectException(TokenExpiredException::class);

        $repository->get('value', 'reset_password');
    }

    public function testGetThrowsWhenTokenIsConsumed(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('value', 'reset_password'));
        $this->refresh();
        $token = $repository->get('value', 'reset_password');
        $token->consume([]);
        $repository->update($token);
        $this->refresh();

        $this->expectException(TokenConsumedException::class);

        $repository->get('value', 'reset_password');
    }

    public function testExistsTellsWhetherAValueIsTakenForAPurpose(): void
    {
        $repository = $this->createRepository();
        self::assertFalse($repository->exists('value', 'reset_password'));

        $repository->create($this->token('value', 'reset_password', duration: '-1 day'));
        $this->refresh();

        self::assertTrue($repository->exists('value', 'reset_password'), 'Even expired, a value stays taken');
        self::assertFalse($repository->exists('value', 'verify_email'));
    }

    public function testFindExistingReturnsTheActiveTokenOfAUserForAPurpose(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('expired', 'reset_password', duration: '-1 day'));
        $repository->create($this->token('active', 'reset_password'));
        $repository->create($this->token('other_purpose', 'verify_email'));
        $repository->create($this->token('other_user', 'reset_password', userId: '43'));
        $this->refresh();

        self::assertSame('active', $repository->findExisting(\stdClass::class, '42', 'reset_password')?->getValue());
        self::assertNull($repository->findExisting(\stdClass::class, '44', 'reset_password'));
        self::assertNull($repository->findExisting(\ArrayObject::class, '42', 'reset_password'));
    }

    public function testFindExistingIgnoresConsumedTokens(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('value', 'reset_password'));
        $this->refresh();
        $token = $repository->get('value', 'reset_password');
        $token->consume([]);
        $repository->update($token);
        $this->refresh();

        self::assertNull($repository->findExisting(\stdClass::class, '42', 'reset_password'));
    }

    public function testPurgeRemovesTokensKeptUntilBeforeAGivenDate(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('outdated', 'reset_password', duration: '-2 days', keep: '+1 day'));
        $repository->create($this->token('kept', 'reset_password', duration: '-2 days', keep: '+3 days'));
        $this->refresh();

        self::assertSame(1, $repository->purge(new DateTimeImmutable()));
        $this->refresh();

        self::assertFalse($repository->exists('outdated', 'reset_password'));
        self::assertTrue($repository->exists('kept', 'reset_password'));
    }

    public function testPurgeCanBeRestrictedToAPurpose(): void
    {
        $repository = $this->createRepository();
        $repository->create($this->token('outdated', 'reset_password', duration: '-2 days', keep: '+1 day'));
        $repository->create($this->token('outdated', 'verify_email', duration: '-2 days', keep: '+1 day'));
        $this->refresh();

        self::assertSame(1, $repository->purge(new DateTimeImmutable(), 'verify_email'));
        $this->refresh();

        self::assertTrue($repository->exists('outdated', 'reset_password'));
        self::assertFalse($repository->exists('outdated', 'verify_email'));
    }

    /**
     * @param string $duration Validity duration, from now
     * @param string $keep     Keep duration, from expiration
     */
    protected function token(
        string $value,
        string $purpose,
        string $userId = '42',
        string $duration = '+1 day',
        string $keep = '+1 month',
    ): Token {
        return new Token(\stdClass::class, $userId, $value, $purpose, $duration, $keep);
    }
}
