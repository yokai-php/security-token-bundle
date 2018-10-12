<?php

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * This archivist is removing all outdated tokens based on the `keepUntil` property.
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DeleteArchivist implements ArchivistInterface
{
    /**
     * @var EntityRepository
     */
    private $tokenRepository;

    /**
     * @param EntityRepository $tokenRepository The token entity repository
     */
    public function __construct(EntityRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @inheritDoc
     */
    public function archive($purpose = null)
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

        return intval($builder->getQuery()->execute());
    }
}
