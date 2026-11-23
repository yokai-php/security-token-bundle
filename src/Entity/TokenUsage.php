<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Entity;

use DateTimeImmutable;
use DateTimeInterface;

class TokenUsage
{
    private int|null $id = null;

    private Token $token;

    private DateTimeImmutable $createdAt;

    /**
     * @var array<string, mixed>
     */
    private array $information = [];

    /**
     * @param array<string, mixed> $information
     */
    public function __construct(Token $token, array $information, DateTimeInterface|null $createdAt = null)
    {
        $this->token = $token;
        $this->information = $information;
        $this->createdAt = $createdAt !== null ? DateTimeImmutable::createFromInterface(
            $createdAt,
        ) : new DateTimeImmutable();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function getInformation(): array
    {
        return $this->information;
    }
}
