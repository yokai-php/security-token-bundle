<?php

namespace Yokai\SecurityTokenBundle\InformationGuesser;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface InformationGuesserInterface
{
    /**
     * @return array
     */
    public function get();
}
