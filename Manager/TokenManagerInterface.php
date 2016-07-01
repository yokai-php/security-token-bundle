<?php

namespace Yokai\SecurityTokenBundle\Manager;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface TokenManagerInterface
{
    /**
     * @param string        $purpose
     * @param UserInterface $user
     *
     * @return Token
     */
    public function create($purpose, UserInterface $user = null);

    /**
     * @param Token         $token
     * @param DateTime|null $at
     */
    public function setUsed(Token $token, DateTime $at = null);
}
