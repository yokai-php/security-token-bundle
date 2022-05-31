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
     * @var array
     */
    private $payload = [];

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var array
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
     * @param class-string $userClass
     * @param string       $userId
     * @param string       $value
     * @param string       $purpose
     * @param string       $validDuration
     * @param string       $keepDuration
     * @param int          $allowedUsages
     * @param array        $payload
     * @param array        $information
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

    /**
     * @return int|null
     */
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

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getCreatedInformation(): array
    {
        return $this->createdInformation;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @return DateTime
     */
    public function getKeepUntil(): DateTime
    {
        return $this->keepUntil;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTime();
    }

    /**
     * @return bool
     */
    public function isConsumed(): bool
    {
        $allowed = $this->getAllowedUsages();
        if ($allowed === 0) {
            return false;
        }

        return $this->getCountUsages() >= $allowed;
    }

    /**
     * @return int
     */
    public function getAllowedUsages(): int
    {
        return $this->allowedUsages;
    }

    /**
     * @return int
     */
    public function getCountUsages(): int
    {
        return count($this->usages);
    }

    /**
     * @return TokenUsage[]
     */
    public function getUsages(): array
    {
        return $this->usages->toArray();
    }

    /**
     * @return TokenUsage|null
     */
    public function getLastUsage(): ?TokenUsage
    {
        return $this->usages->last();
    }

    /**
     * @param array         $information
     * @param DateTime|null $date
     *
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
