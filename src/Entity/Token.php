<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use LogicException;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class Token
{
    private int|null $id;

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

    private DateTime $createdAt;

    /**
     * @var array<string, mixed>
     */
    private array $createdInformation;

    private int $allowedUsages;

    private DateTime $expiresAt;

    private DateTime $keepUntil;

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
        $this->createdAt = new DateTime();
        $this->expiresAt = (new DateTime())->modify($validDuration);
        $this->keepUntil = (clone $this->expiresAt)->modify($keepDuration);
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

    public function getCreatedAt(): DateTime
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

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function getKeepUntil(): DateTime
    {
        return $this->keepUntil;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTime();
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
    public function consume(array $information, DateTime|null $date = null): void
    {
        if ($this->isConsumed()) {
            throw new LogicException(
                \sprintf('Token "%d" is already consumed.', $this->id),
            );
        }

        $this->usages->add(new TokenUsage($this, $information, $date));
    }
}
