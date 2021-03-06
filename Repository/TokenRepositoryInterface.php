<?php

namespace Yokai\SecurityTokenBundle\Repository;

use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;

/**
 * A token repository handles token persistence.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenRepositoryInterface
{
    /**
     * Gets a Token instance from storage.
     *
     * @param string $value   The token value
     * @param string $purpose The token purpose
     *
     * @return Token
     *
     * @throws TokenNotFoundException if the token cannot be found
     * @throws TokenExpiredException if the token is expired
     * @throws TokenConsumedException if the token is consumed
     */
    public function get(string $value, string $purpose): Token;

    /**
     * Find existing and active token for user and purpose.
     *
     * @param string $userClass The user class
     * @param string $userId    The user identifier
     * @param string $purpose   The token purpose
     *
     * @return Token|null
     */
    public function findExisting(string $userClass, string $userId, string $purpose): ?Token;

    /**
     * Tell whether or not it exists a token for given purpose and value.
     *
     * @param string $value   A token value
     * @param string $purpose A token purpose
     *
     * @return bool
     */
    public function exists(string $value, string $purpose): bool;

    /**
     * Add a token to storage.
     *
     * @param Token $token The token to add
     */
    public function create(Token $token): void;

    /**
     * Update a token to storage.
     *
     * @param Token $token The token to update
     */
    public function update(Token $token): void;
}
