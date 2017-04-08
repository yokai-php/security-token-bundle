<?php

namespace Yokai\SecurityTokenBundle\Archive;

use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DeleteArchivist implements ArchivistInterface
{
    /**
     * @var EntityRepository
     */
    private $tokenRepository;

    /**
     * @var EntityRepository
     */
    private $tokenUsageRepository;

    /**
     * @param EntityRepository $tokenRepository
     * @param EntityRepository $tokenUsageRepository
     */
    public function __construct(EntityRepository $tokenRepository, EntityRepository $tokenUsageRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokenUsageRepository = $tokenUsageRepository;
    }

    /**
     * @inheritDoc
     */
    public function archive($purpose = null, DateTime $before = null)
    {
        $builder = $this->tokenRepository->createQueryBuilder('token')
            ->delete($this->tokenRepository->getClassName(), 'token');

        $subBuilder = $this->tokenUsageRepository->createQueryBuilder('token_usage')
            ->select('token_usage.id');

        $builder->where($builder->expr()->in('token.id', $subBuilder->getDQL()));

        if ($purpose) {
            $builder
                ->andWhere($builder->expr()->eq('token.purpose', ':purpose'))
                ->setParameter('purpose', $purpose)
            ;
        }

        if ($before) {
            $builder
                ->andWhere($builder->expr()->lt('token.createdAt', ':before'))
                ->setParameter('before', $before)
            ;
        }

        return intval($builder->getQuery()->execute());
    }
}
