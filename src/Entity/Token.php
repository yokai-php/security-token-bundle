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
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var class-string
     */
    private $userClass;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var array<string, mixed>
     */
    private $payload = [];

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var array<string, mixed>
     */
    private $createdInformation = [];

    /**
     * @var integer
     */
    private $allowedUsages;

    /**
     * @var DateTime
     */
    private $expiresAt;

    /**
     * @var DateTime
     */
    private $keepUntil;

    /**
     * @var Collection<TokenUsage>
     */
    private $usages;

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
        array $information = []
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

    public function getId(): ?int
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
        return count($this->usages);
    }

    /**
     * @return array<TokenUsage>
     */
    public function getUsages(): array
    {
        return $this->usages->toArray();
    }

    public function getLastUsage(): ?TokenUsage
    {
        return $this->usages->last();
    }

    /**
     * @throws LogicException
     */
    public function consume(array $information, DateTime $date = null): void
    {
        if ($this->isConsumed()) {
            throw new LogicException(
                sprintf('Token "%d" is already consumed.', $this->id)
            );
        }

        $this->usages->add(new TokenUsage($this, $information, $date));
    }
}
