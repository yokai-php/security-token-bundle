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
    private $repository;

    /**
     * @param EntityRepository $repository
     */
    public function __construct($repository)
    {
        if (!$repository instanceof EntityRepository) {
            throw new \RuntimeException();//todo
        }
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function archive($purpose = null, DateTime $before = null)
    {
        $builder = $this->repository->createQueryBuilder('token')
            ->delete($this->repository->getClassName(), 'token');

        $builder->where($builder->expr()->isNotNull('token.usedAt'));

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
