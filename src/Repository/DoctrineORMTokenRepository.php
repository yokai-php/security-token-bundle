<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Repository;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;

/**
 * Doctrine ORM token repository;
 *
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
final class DoctrineORMTokenRepository implements TokenRepositoryInterface
{
    public function __construct(
        /**
         * The token entity manager
         */
        private readonly EntityManagerInterface $manager,
        /**
         * The token entity repository
         * @var EntityRepository<Token>
         */
        private readonly EntityRepository $repository,
    ) {
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

    public function findExisting(string $userClass, string $userId, string $purpose): Token|null
    {
        /** @var list<Token> $tokens */
        $tokens = $this->repository->createQueryBuilder('token')
            ->where('token.userClass = :userClass')
            ->andWhere('token.userId = :userId')
            ->andWhere('token.purpose = :purpose')
            ->andWhere('token.expiresAt > :now')
            ->orderBy('token.createdAt', 'DESC')
            ->setParameter('userClass', $userClass)
            ->setParameter('userId', $userId)
            ->setParameter('purpose', $purpose)
            ->setParameter('now', new DateTimeImmutable())
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

        return \intval($result) > 0;
    }

    public function purge(DateTimeInterface $before, string|null $purpose = null): int
    {
        $condition = 'token.keepUntil < :before';
        // a mutable date would be bound with the deprecated "datetime" type
        $parameters = ['before' => DateTimeImmutable::createFromInterface($before)];
        if ($purpose !== null) {
            $condition .= ' AND token.purpose = :purpose';
            $parameters['purpose'] = $purpose;
        }

        // Usages are removed explicitly: the database may not enforce the "ON DELETE CASCADE" foreign key
        $this->manager
            ->createQuery(\sprintf(
                'DELETE FROM %s usage WHERE usage.token IN (SELECT token.id FROM %s token WHERE %s)',
                TokenUsage::class,
                Token::class,
                $condition,
            ))
            ->execute($parameters)
        ;

        /** @var int|string $result */
        $result = $this->manager
            ->createQuery(\sprintf('DELETE FROM %s token WHERE %s', Token::class, $condition))
            ->execute($parameters)
        ;

        return \intval($result);
    }

    private function save(Token $token): void
    {
        $this->manager->persist($token);
        $this->manager->flush();
    }
}
