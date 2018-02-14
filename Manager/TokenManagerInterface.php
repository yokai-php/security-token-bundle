<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;

/**
 * A token manager is the entry point to deal with tokens.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenManagerInterface
{
    /**
     * Get a token instance.
     *
     * @param string $purpose The token purpose
     * @param string $value   The token value
     *
     * @return Token
     *
     * @throws TokenNotFoundException if the token cannot be found
     * @throws TokenExpiredException if the token is expired
     * @throws TokenConsumedException if the token is consumed
     */
    public function get($purpose, $value);

    /**
     * Create a token.
     *
     * @param string $purpose The token purpose
     * @param mixed  $user    The user to associate to the token
     * @param array  $payload Some additional payload for the token
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
     * Consume a token.
     *
     * @param Token         $token The token to consume
     * @param DateTime|null $at    The date/time at which the token was consumed (defaults to now)
     */
    public function consume(Token $token, DateTime $at = null);

    /**
     * Get the user associated to a token.
     *
     * @param Token $token The token
     *
     * @return mixed
     */
    public function getUser(Token $token);
}
