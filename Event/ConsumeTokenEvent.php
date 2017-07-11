<?php

namespace Yokai\SecurityTokenBundle\Event;

use DateTime;
use Symfony\Component\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ConsumeTokenEvent extends Event
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @var DateTime|null
     */
    private $at;

    /**
     * @var array
     */
    private $information;

    /**
     * @param Token         $token
     * @param DateTime|null $at
     * @param array         $information
     */
    public function __construct(Token $token, DateTime $at = null, array $information)
    {
        $this->token = $token;
        $this->at = $at;
        $this->information = $information;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return DateTime|null
     */
    public function getAt()
    {
        return $this->at;
    }

    /**
     * @return array
     */
    public function getInformation()
    {
        return $this->information;
    }
}
