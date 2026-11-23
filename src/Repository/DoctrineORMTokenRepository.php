<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Repository;

use DateTime;
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
            ],
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
        /** @var Token[] $tokens */
        $tokens = $this->repository->createQueryBuilder('token')
            ->where('token.userClass = :userClass')
            ->andWhere('token.userId = :userId')
            ->andWhere('token.purpose = :purpose')
            ->andWhere('token.expiresAt > :now')
            ->orderBy('token.createdAt', 'DESC')
            ->setParameter('userClass', $userClass)
            ->setParameter('userId', $userId)
            ->setParameter('purpose', $purpose)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult()
        ;

        // consumption depends on usages count, which is easier to check on the object
        foreach ($tokens as $token) {
            if (!$token->isConsumed()) {
                return $token;
            }
        }

        return null;
    }

    public function create(Token $token): void
    {
        $this->save($token);
    }

    public function update(Token $token): void
    {
        $this->save($token);
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

    private function save(Token $token): void
    {
        $this->manager->persist($token);

        if ((new \ReflectionMethod($this->manager, 'flush'))->getNumberOfParameters() > 0) {
            $this->manager->flush($token);
        } else {
            $this->manager->flush();
        }
    }
}
