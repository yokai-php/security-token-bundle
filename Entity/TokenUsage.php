<?php

namespace Yokai\SecurityTokenBundle\Entity;

use DateTime;

class TokenUsage
{
    /**
     * @var int
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
     * @param Token    $token
     * @param array    $information
     * @param DateTime $createdAt
     */
    public function __construct(Token $token, array $information, DateTime $createdAt = null)
    {
        $this->token = $token;
        $this->information = $information;
        $this->createdAt = $createdAt ?: new DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getInformation()
    {
        return $this->information;
    }
}
