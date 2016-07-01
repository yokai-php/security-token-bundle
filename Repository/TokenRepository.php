<?php

namespace Yokai\SecurityTokenBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\InvalidTokenException;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TokenRepository extends EntityRepository implements TokenRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function get($value, $purpose)
    {
        $token = $this->findOneBy(
            [
                'value' => $value,
                'purpose' => $purpose,
            ]
        );

        if (!$token instanceof Token) {
            throw InvalidTokenException::notFound($value, $purpose);
        }
        if ($token->isExpired()) {
            throw InvalidTokenException::expired($value, $purpose, $token->getExpiresAt());
        }
        if ($token->isUsed()) {
            throw InvalidTokenException::used($value, $purpose, $token->getUsedAt());
        }

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function create(Token $token)
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush($token);
    }

    /**
     * @inheritdoc
     */
    public function update(Token $token)
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush($token);
    }

    /**
     * @inheritdoc
     */
    public function exists($value, $purpose)
    {
        $builder = $this->createQueryBuilder('token');
        $builder
            ->select('COUNT(token.id)')
            ->where('token.value = :value')
            ->andWhere('token.purpose = :purpose')
            ->setParameters(
                [
                    'value' => $value,
                    'purpose' => $purpose,
                ]
            )
        ;

        return $builder->getQuery()->getSingleScalarResult() > 0;
    }
}
