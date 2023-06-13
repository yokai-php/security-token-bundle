<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Entity;

use DateTime;

class TokenUsage
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var array<string, mixed>
     */
    private $information = [];

    /**
     * @param array<string, mixed> $information
     */
    public function __construct(Token $token, array $information, DateTime $createdAt = null)
    {
        $this->token = $token;
        $this->information = $information;
        $this->createdAt = $createdAt ?: new DateTime();
    }

    public function getId(): ?int
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
