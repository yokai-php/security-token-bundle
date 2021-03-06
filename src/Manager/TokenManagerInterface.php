<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;

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
    public function get(string $purpose, string $value): Token;

    /**
     * Create a token.
     *
     * @param string $purpose The token purpose
     * @param mixed  $user    The user to associate to the token
     * @param array  $payload Some additional payload for the token
     *
     * @return Token
     */
    public function create(string $purpose, $user, array $payload = []): Token;

    /**
     * Consume a token.
     *
     * @param Token         $token The token to consume
     * @param DateTime|null $at    The date/time at which the token was consumed (defaults to now)
     */
    public function consume(Token $token, DateTime $at = null): void;

    /**
     * Get the user associated to a token.
     *
     * @param Token $token The token
     *
     * @return mixed
     */
    public function getUser(Token $token);
}
