<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * This archivist is removing all outdated tokens based on the `keepUntil` property.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class DeleteArchivist implements ArchivistInterface
{
    public function __construct(
        /**
         * The token repository
         */
        private readonly TokenRepositoryInterface $repository,
    ) {
    }

    public function archive(string|null $purpose = null): int
    {
        return $this->repository->purge(new DateTime(), $purpose);
    }
}
