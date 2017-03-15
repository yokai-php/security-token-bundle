<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface TokenFactoryInterface
{
    /**
     * @param mixed  $user
     * @param string $purpose
     *
     * @return Token
     */
    public function create($user, $purpose);
}
