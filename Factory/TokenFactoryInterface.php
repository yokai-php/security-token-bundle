<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * A token factory is responsible for creating Token instances.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenFactoryInterface
{
    /**
     * Create a new token.
     *
     * @param mixed  $user    The to associated to the token
     * @param string $purpose The token purpose
     * @param array  $payload The token payload
     *
     * @return Token
     */
    public function create($user, $purpose, array $payload = []);
}
