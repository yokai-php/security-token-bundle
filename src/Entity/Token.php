<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LogicException;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class Token
{
    private int|null $id = null;

    /**
     * @var class-string
     */
    private string $userClass;

    private string $userId;

    private string $value;

    private string $purpose;

    /**
     * @var array<string, mixed>
     */
    private array $payload;

    private DateTimeImmutable $createdAt;

    /**
     * @var array<string, mixed>
     */
    private array $createdInformation;

    private int $allowedUsages;

    private DateTimeImmutable $expiresAt;

    private DateTimeImmutable $keepUntil;

    /**
     * @var Collection<int, TokenUsage>
     */
    private Collection $usages;

    /**
     * @param class-string         $userClass
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $information
     */
    public function __construct(
        string $userClass,
        string $userId,
        string $value,
        string $purpose,
        string $validDuration,
        string $keepDuration,
        int $allowedUsages = 1,
        array $payload = [],
        array $information = [],
    ) {
        $this->userClass = $userClass;
        $this->userId = $userId;
        $this->value = $value;
        $this->purpose = $purpose;
        $this->createdAt = new DateTimeImmutable();
        $this->expiresAt = $this->createdAt->modify($validDuration);
        $this->keepUntil = $this->expiresAt->modify($keepDuration);
        $this->allowedUsages = $allowedUsages;
        $this->payload = $payload;
        $this->createdInformation = $information;
        $this->usages = new ArrayCollection();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * @return class-string
     */
    public function getUserClass(): string
    {
        return $this->userClass;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCreatedInformation(): array
    {
        return $this->createdInformation;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getKeepUntil(): DateTimeImmutable
    {
        return $this->keepUntil;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }

    public function isConsumed(): bool
    {
        $allowed = $this->getAllowedUsages();
        if ($allowed === 0) {
            return false;
        }

        return $this->getCountUsages() >= $allowed;
    }

    public function getAllowedUsages(): int
    {
        return $this->allowedUsages;
    }

    public function getCountUsages(): int
    {
        return \count($this->usages);
    }

    /**
     * @return array<int, TokenUsage>
     */
    public function getUsages(): array
    {
        return $this->usages->toArray();
    }

    public function getLastUsage(): TokenUsage|null
    {
        return $this->usages->last() ?: null;
    }

    /**
     * @param array<string, mixed> $information
     *
     * @throws LogicException
     */
    public function consume(array $information, DateTimeInterface|null $date = null): void
    {
        if ($this->isConsumed()) {
            throw new LogicException(
                \sprintf('Token for purpose "%s" is already consumed.', $this->purpose),
            );
        }

        $this->usages->add(new TokenUsage($this, $information, $date));
    }
}
