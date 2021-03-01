<?php

namespace Yokai\SecurityTokenBundle\Archive;

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
     * @param string|null $purpose The token purpose
     *
     * @return integer
     */
    public function archive(string $purpose = null): int;
}
