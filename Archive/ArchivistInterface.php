<?php

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;

/**
 * An archivist deals with Token that may be outdated, and decide what to do with those.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
interface ArchivistInterface
{
    /**
     * Archive outdated tokens.
     *
     * @param string|null   $purpose The token purpose
     * @param DateTime|null $before  @deprecated since version 2.2 and will be removed in 3.0.
     *
     * @return integer
     */
    public function archive($purpose = null, DateTime $before = null);
}
