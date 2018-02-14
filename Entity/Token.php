<?php

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
     * @var int
     */
    private $id;

    /**
     * @var string
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
     * @var Collection|TokenUsage[]
     */
    private $usages;

    /**
     * @param string  $userClass
     * @param string  $userId
     * @param string  $value
     * @param string  $purpose
     * @param string  $validDuration
     * @param string  $keepDuration
     * @param integer $allowedUsages
     * @param array   $payload
     * @param array   $information
     */
    public function __construct(
        $userClass,
        $userId,
        $value,
        $purpose,
        $validDuration,
        $keepDuration,
        $allowedUsages = 1,
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
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
     * @return DateTime
     */
    public function getKeepUntil()
    {
        return $this->keepUntil;
    }

    /**
     * @return DateTime|null
     *
     * @deprecated since version 2.2 and will be removed in 3.0
     */
    public function getUsedAt()
    {
        @trigger_error(
            'The '.__METHOD__
            .' method is deprecated since version 2.2 and will be removed in 3.0. Use the getLastUsage() method instead.',
            E_USER_DEPRECATED
        );

        $usage = $this->getLastUsage();
        if (null === $usage) {
            return null;
        }

        return $usage->getCreatedAt();
    }

    /**
     * @param DateTime $usedAt
     *
     * @deprecated since version 2.2 and will be removed in 3.0
     */
    public function setUsedAt($usedAt)
    {
        @trigger_error(
            'The '.__METHOD__
            .' method is deprecated since version 2.2 and will be removed in 3.0. Use the getLastUsage() method instead.',
            E_USER_DEPRECATED
        );

        $this->consume([], $usedAt);
    }

    /**
     * @return array
     *
     * @deprecated since version 2.2 and will be removed in 3.0
     */
    public function getUsedInformation()
    {
        @trigger_error(
            'The '.__METHOD__
            .' method is deprecated since version 2.2 and will be removed in 3.0. Use the getLastUsage() method instead.',
            E_USER_DEPRECATED
        );

        $usage = $this->getLastUsage();
        if (null === $usage) {
            return null;
        }

        return $usage->getInformation();
    }

    /**
     * @param array $usedInformation
     *
     * @deprecated since version 2.2 and will be removed in 3.0
     */
    public function setUsedInformation($usedInformation)
    {
        @trigger_error(
            'The '.__METHOD__
            .' method is deprecated since version 2.2 and will be removed in 3.0. Use the getLastUsage() method instead.',
            E_USER_DEPRECATED
        );

        $this->consume($usedInformation);
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        return $this->expiresAt < new DateTime();
    }

    /**
     * @deprecated since 2.3 and will be removed in 3.0. Use isConsumed instead.
     * @return boolean
     */
    public function isUsed()
    {
        @trigger_error(
            __METHOD__.' is deprecated. Use '.__CLASS__.'::isConsumed instead',
            E_USER_DEPRECATED
        );

        return $this->isConsumed();
    }

    /**
     * @return boolean
     */
    public function isConsumed()
    {
        return $this->getCountUsages() >= $this->getAllowedUsages();
    }

    /**
     * @return int
     */
    public function getAllowedUsages()
    {
        return $this->allowedUsages;
    }

    /**
     * @return int
     */
    public function getCountUsages()
    {
        return count($this->usages);
    }

    /**
     * @return TokenUsage[]
     */
    public function getUsages()
    {
        return $this->usages->toArray();
    }

    /**
     * @return TokenUsage|null
     */
    public function getLastUsage()
    {
        return $this->usages->last();
    }

    /**
     * @param array         $information
     * @param DateTime|null $date
     */
    public function consume(array $information, DateTime $date = null)
    {
        if ($this->isConsumed()) {
            throw new LogicException(
                sprintf('Token "%d" is already consumed.', $this->id)
            );
        }

        $this->usages->add(new TokenUsage($this, $information, $date));
    }
}
