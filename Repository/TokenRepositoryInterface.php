<?php

namespace Yokai\SecurityTokenBundle\Repository;

use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Exception\TokenUsedException;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenRepositoryInterface
{
    /**
     * @param string $value
     * @param string $purpose
     *
     * @return Token
     *
     * @throws TokenNotFoundException if the token cannot be found
     * @throws TokenExpiredException if the token is expired
     * @throws TokenUsedException if the token is used
     */
    public function get($value, $purpose);

    /**
     * @param string $userClass
     * @param string $userId
     * @param string $purpose
     *
     * @return Token|null
     */
    public function findExisting($userClass, $userId, $purpose);

    /**
     * @param string $value
     * @param string $purpose
     *
     * @return boolean
     */
    public function exists($value, $purpose);

    /**
     * @param Token $token
     */
    public function create(Token $token);

    /**
     * @param Token $token
     */
    public function update(Token $token);
}
