<?php

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface ArchivistInterface
{
    /**
     * @param string|null   $purpose
     * @param DateTime|null $before  @deprecated since version 2.2 and will be removed in 3.0.
     *
     * @return integer
     */
    public function archive($purpose = null, DateTime $before = null);
}
