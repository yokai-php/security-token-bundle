<?php

namespace Yokai\SecurityTokenBundle\Generator;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
