<?php

namespace Yokai\SecurityTokenBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Repository\DoctrineORMTokenRepository;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class DoctrineORMTokenRepositoryTest extends TestCase
{
    /**
     * @var EntityManager|ObjectProphecy
     */
    private $manager;

    /**
     * @var EntityRepository|ObjectProphecy
     */
    private $repository;

    protected function setUp(): void
    {
        $this->manager = $this->prophesize(EntityManager::class);
        $this->repository = $this->prophesize(EntityRepository::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->manager,
            $this->repository
        );
    }

    protected function repository(): DoctrineORMTokenRepository
    {
        return new DoctrineORMTokenRepository($this->manager->reveal(), $this->repository->reveal());
    }

    /**
     * @test
     */
    public function it_throw_exception_if_token_not_found(): void
    {
        $this->expectException(TokenNotFoundException::class);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     */
    public function it_throw_exception_if_token_expired(): void
    {
        $this->expectException(TokenExpiredException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '-1 day', '+1 month', 1, []);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     */
    public function it_throw_exception_if_token_used_single_time(): void
    {
        $this->expectException(TokenConsumedException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1);
        $token->consume(['info'], new \DateTime());

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     */
    public function it_throw_exception_if_token_used_multiple_times(): void
    {
        $this->expectException(TokenConsumedException::class);

        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 2);
        $token->consume(['info'], new \DateTime());
        $token->consume(['info'], new \DateTime());

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     */
    public function it_get_valid_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $got = $this->repository()->get('unique', 'init_password');

        self::assertSame($token, $got);
    }

    /**
     * @test
     */
    public function it_create_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->manager->persist($token)
            ->shouldBeCalledTimes(1);
        $this->manager->flush($token)
            ->shouldBeCalledTimes(1);

        $this->repository()->create($token);
    }

    /**
     * @test
     */
    public function it_update_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique', 'init_password', '+1 day', '+1 month', 1, []);

        $this->manager->persist($token)
            ->shouldBeCalledTimes(1);
        $this->manager->flush($token)
            ->shouldBeCalledTimes(1);

        $this->repository()->update($token);
    }
}
