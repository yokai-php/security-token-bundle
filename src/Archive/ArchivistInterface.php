<?php

declare(strict_types=1);

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
     * @return int Count archived tokens
     */
    public function archive(string|null $purpose = null): int;
}
