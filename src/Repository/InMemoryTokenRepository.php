<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Repository;

use DateTimeInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;

/**
 * Keeps tokens in memory, for the time of the PHP process: meant for tests.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class InMemoryTokenRepository implements TokenRepositoryInterface
{
    /**
     * @var array<string, Token> Tokens, indexed by purpose and value
     */
    private array $tokens = [];

    public function get(string $value, string $purpose): Token
    {
        $token = $this->tokens[$this->key($value, $purpose)] ?? null;
        if ($token === null) {
            throw TokenNotFoundException::create($value, $purpose);
        }
        if ($token->isExpired()) {
            throw TokenExpiredException::create($value, $purpose, $token->getExpiresAt());
        }
        if ($token->isConsumed()) {
            throw TokenConsumedException::create($value, $purpose, $token->getCountUsages());
        }

        return $token;
    }

    public function findExisting(string $userClass, string $userId, string $purpose): Token|null
    {
        foreach ($this->tokens as $token) {
            if (
                $token->getUserClass() === $userClass
                && $token->getUserId() === $userId
                && $token->getPurpose() === $purpose
                && !$token->isExpired()
                && !$token->isConsumed()
            ) {
                return $token;
            }
        }

        return null;
    }

    public function exists(string $value, string $purpose): bool
    {
        return isset($this->tokens[$this->key($value, $purpose)]);
    }

    public function create(Token $token): void
    {
        $this->tokens[$this->key($token->getValue(), $token->getPurpose())] = $token;
    }

    public function update(Token $token): void
    {
        $this->create($token);
    }

    public function purge(DateTimeInterface $before, string|null $purpose = null): int
    {
        $count = 0;
        foreach ($this->tokens as $key => $token) {
            if ($token->getKeepUntil() < $before && ($purpose === null || $token->getPurpose() === $purpose)) {
                unset($this->tokens[$key]);
                $count++;
            }
        }

        return $count;
    }

    private function key(string $value, string $purpose): string
    {
        return $purpose . "\0" . $value;
    }
}
