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
final class ConsumeTokenEvent extends Event
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
     * @var array<string, mixed>
     */
    private $information;

    /**
     * @param Token                $token       The consumed token
     * @param DateTime|null        $at          Date/time at which the token has been consumed
     * @param array<string, mixed> $information Some context information
     */
    public function __construct(Token $token, DateTime|null $at, array $information)
    {
        $this->token = $token;
        $this->at = $at;
        $this->information = $information;
    }

    /**
     * The consumed token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Date/time at which the token has been consumed
     */
    public function getAt(): DateTime|null
    {
        return $this->at;
    }

    /**
     * Some context information
     *
     * @return array<string, mixed>
     */
    public function getInformation(): array
    {
        return $this->information;
    }
}
