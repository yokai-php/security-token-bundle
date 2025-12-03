<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Entity\Token;

/**
 * This archivist is removing all outdated tokens based on the `keepUntil` property.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class DeleteArchivist implements ArchivistInterface
{
    /**
     * @var EntityRepository<Token>
     */
    private $tokenRepository;

    /**
     * @param EntityRepository<Token> $tokenRepository The token entity repository
     */
    public function __construct(EntityRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function archive(string|null $purpose = null): int
    {
        $builder = $this->tokenRepository->createQueryBuilder('token')
            ->delete($this->tokenRepository->getClassName(), 'token');

        $builder
            ->where($builder->expr()->lt('token.keepUntil', ':now'))
            ->setParameter('now', new DateTime())
        ;

        if ($purpose) {
            $builder
                ->andWhere($builder->expr()->eq('token.purpose', ':purpose'))
                ->setParameter('purpose', $purpose)
            ;
        }

        /** @var int|string $result */
        $result = $builder->getQuery()->execute();

        return \intval($result);
    }
}
