<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenManagerInterface
{
    /**
     * @param string $purpose
     * @param string $value
     *
     * @return Token
     */
    public function get($purpose, $value);

    /**
     * @param string $purpose
     * @param mixed  $user
     * @param array  $payload
     *
     * @return Token
     */
    public function create($purpose, $user, array $payload = []);

    /**
     * @param Token         $token
     * @param DateTime|null $at
     *
     * @deprecated since version 2.2 and will be removed in 3.0
     */
    public function setUsed(Token $token, DateTime $at = null);

    /**
     * @param Token         $token
     * @param DateTime|null $at
     */
    public function consume(Token $token, DateTime $at = null);

    /**
     * @param Token $token
     *
     * @return mixed
     */
    public function getUser(Token $token);
}
