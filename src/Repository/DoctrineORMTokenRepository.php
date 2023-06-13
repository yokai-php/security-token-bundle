<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;

/**
 * Doctrine ORM token repository;
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DoctrineORMTokenRepository implements TokenRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @param EntityManager    $manager    The token entity manager
     * @param EntityRepository $repository The token entity repository
     */
    public function __construct(EntityManager $manager, EntityRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function get(string $value, string $purpose): Token
    {
        $token = $this->repository->findOneBy(
            [
                'value' => $value,
                'purpose' => $purpose,
            ]
        );

        if (!$token instanceof Token) {
            throw TokenNotFoundException::create($value, $purpose);
        }
        if ($token->isExpired()) {
            throw TokenExpiredException::create($value, $purpose, $token->getExpiresAt());
        }
        if ($token->isConsumed()) {
            throw TokenConsumedException::create($value, $purpose, $token->getCountUsages());
        }

        return $token;
    }

    public function findExisting(string $userClass, string $userId, string $purpose): ?Token
    {
        $token = $this->repository->findOneBy(
            [
                'userClass' => $userClass,
                'userId' => $userId,
                'purpose' => $purpose,
            ]
        );
        if (!$token instanceof Token) {
            return null;
        }
        if ($token->isConsumed() || $token->isExpired()) {
            return null;
        }

        return $token;
    }

    public function create(Token $token): void
    {
        $this->manager->persist($token);
        $this->manager->flush($token);
    }

    public function update(Token $token): void
    {
        $this->manager->persist($token);
        $this->manager->flush($token);
    }

    public function exists(string $value, string $purpose): bool
    {
        $builder = $this->repository->createQueryBuilder('token');
        $builder
            ->select('COUNT(token.id)')
            ->where('token.value = :value')
            ->andWhere('token.purpose = :purpose')
            ->setParameter('value', $value)
            ->setParameter('purpose', $purpose)
        ;

        /** @var string|int $result */
        $result = $builder->getQuery()->getSingleScalarResult();

        return intval($result) > 0;
    }
}
