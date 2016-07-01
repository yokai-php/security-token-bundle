<?php

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface ArchivistInterface
{
    /**
     * @param string|null   $purpose
     * @param DateTime|null $before
     *
     * @return integer
     */
    public function archive($purpose = null, DateTime $before = null);
}
