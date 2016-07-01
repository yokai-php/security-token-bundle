<?php

namespace Yokai\SecurityTokenBundle\Generator;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
