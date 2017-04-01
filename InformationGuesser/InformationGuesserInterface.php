<?php

namespace Yokai\SecurityTokenBundle\InformationGuesser;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface InformationGuesserInterface
{
    /**
     * @return array
     */
    public function get();
}
