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
     * @var array
     */
    private $information = [];

    /**
     * @param Token         $token
     * @param array         $information
     * @param DateTime|null $createdAt
     */
    public function __construct(Token $token, array $information, DateTime $createdAt = null)
    {
        $this->token = $token;
        $this->information = $information;
        $this->createdAt = $createdAt ?: new DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
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
    public function getInformation(): array
    {
        return $this->information;
    }
}
