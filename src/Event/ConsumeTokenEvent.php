<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Event;

use DateTime;
use Symfony\Contracts\EventDispatcher\Event;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * Event being dispatched before a Token is consumed.
 *
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
     * @param Token         $token       The consumed token
     * @param DateTime|null $at          Date/time at which the token has been consumed
     * @param array         $information Some context information
     */
    public function __construct(Token $token, ?DateTime $at, array $information)
    {
        $this->token = $token;
        $this->at = $at;
        $this->information = $information;
    }

    /**
     * The consumed token
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Date/time at which the token has been consumed
     *
     * @return DateTime|null
     */
    public function getAt(): ?DateTime
    {
        return $this->at;
    }

    /**
     * Some context information
     *
     * @return array
     */
    public function getInformation(): array
    {
        return $this->information;
    }
}
