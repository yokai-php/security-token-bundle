<?php

namespace Yokai\SecurityTokenBundle\Factory;

use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface TokenFactoryInterface
{
    /**
     * @param UserInterface $user
     * @param string        $purpose
     *
     * @return Token
     */
    public function create(UserInterface $user, $purpose);
}
