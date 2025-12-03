<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Entity;

use DateTime;

class TokenUsage
{
    private int|null $id;

    private Token $token;

    private DateTime $createdAt;

    /**
     * @var array<string, mixed>
     */
    private array $information = [];

    /**
     * @param array<string, mixed> $information
     */
    public function __construct(Token $token, array $information, DateTime|null $createdAt = null)
    {
        $this->token = $token;
        $this->information = $information;
        $this->createdAt = $createdAt ?: new DateTime();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getCreatedAt(): DateTime
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
