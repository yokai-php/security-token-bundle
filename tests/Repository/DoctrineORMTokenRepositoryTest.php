<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Repository\DoctrineORMTokenRepository;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class DoctrineORMTokenRepositoryTest extends TestCase
{
    /**
     * @var MockObject<EntityManager>
     */
    private $manager;

    /**
     * @var MockObject<EntityRepository>
     */
    private $repository;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->manager,
            $this->repository,
        );
    }

    protected function repository(): DoctrineORMTokenRepository
    {
        return new DoctrineORMTokenRepository($this->manager, $this->repository);
    }

    public function testIt_throw_exception_if_token_not_found(): void
    {
        $this->expectException(TokenNotFoundException::class);

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['value' => 'unique', 'purpose' => 'init_password'])
            ->willReturn(null);

        $this->repository()->get('unique', 'init_password');
    }

    public function testIt_throw_exception_if_token_expired(): void
    {
        $this->expectException(TokenExpiredException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '-1 day', '+1 month', 1, []);

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['value' => 'unique', 'purpose' => 'init_password'])
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    public function testIt_throw_exception_if_token_used_single_time(): void
    {
        $this->expectException(TokenConsumedException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1);
        $token->consume(['info'], new \DateTime());

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['value' => 'unique', 'purpose' => 'init_password'])
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    public function testIt_throw_exception_if_token_used_multiple_times(): void
    {
        $this->expectException(TokenConsumedException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 2);
        $token->consume(['info'], new \DateTime());
        $token->consume(['info'], new \DateTime());

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['value' => 'unique', 'purpose' => 'init_password'])
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    public function testIt_get_valid_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['value' => 'unique', 'purpose' => 'init_password'])
            ->willReturn($token);

        $got = $this->repository()->get('unique', 'init_password');

        self::assertSame($token, $got);
    }

    public function testIt_create_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->manager->expects(self::once())
            ->method('persist')
            ->with($token);
        if ((new \ReflectionMethod(EntityManager::class, 'flush'))->getNumberOfParameters() > 0) {
            $this->manager->expects(self::once())
                ->method('flush')
                ->with($token);
        } else {
            $this->manager->expects(self::once())
                ->method('flush');
        }

        $this->repository()->create($token);
    }

    public function testIt_update_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->manager->expects(self::once())
            ->method('persist')
            ->with($token);
        if ((new \ReflectionMethod(EntityManager::class, 'flush'))->getNumberOfParameters() > 0) {
            $this->manager->expects(self::once())
                ->method('flush')
                ->with($token);
        } else {
            $this->manager->expects(self::once())
                ->method('flush');
        }

        $this->repository()->update($token);
    }
}
