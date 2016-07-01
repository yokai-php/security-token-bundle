<?php

namespace Yokai\SecurityTokenBundle\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class Token
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var array
     */
    private $createdInformation = [];

    /**
     * @var DateTime
     */
    private $expiresAt;

    /**
     * @var DateTime|null
     */
    private $usedAt;

    /**
     * @var array
     */
    private $usedInformation;

    /**
     * @param UserInterface $user
     * @param string        $value
     * @param string        $purpose
     * @param string        $duration
     * @param array         $information
     */
    public function __construct(UserInterface $user, $value, $purpose, $duration, array $information)
    {
        $this->user = $user;
        $this->value = $value;
        $this->purpose = $purpose;
        $this->createdAt = new DateTime();
        $this->expiresAt = (new DateTime())->modify($duration);
        $this->createdInformation = $information;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
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
    public function getCreatedInformation()
    {
        return $this->createdInformation;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUsedAt()
    {
        return $this->usedAt;
    }

    /**
     * @param DateTime $usedAt
     */
    public function setUsedAt($usedAt)
    {
        $this->usedAt = $usedAt;
    }

    /**
     * @return array
     */
    public function getUsedInformation()
    {
        return $this->usedInformation;
    }

    /**
     * @param array $usedInformation
     */
    public function setUsedInformation($usedInformation)
    {
        $this->usedInformation = $usedInformation;
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        return $this->expiresAt < new DateTime();
    }

    /**
     * @return boolean
     */
    public function isUsed()
    {
        return null === $this->usedAt;
    }
}
