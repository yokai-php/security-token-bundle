<?php

namespace Yokai\SecurityTokenBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Entity\Token;
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

    protected function setUp()
    {
        $this->manager = $this->prophesize(EntityManager::class);
        $this->repository = $this->prophesize(EntityRepository::class);
    }

    protected function tearDown()
    {
        unset(
            $this->manager,
            $this->repository
        );
    }

    protected function repository()
    {
        return new DoctrineORMTokenRepository($this->manager->reveal(), $this->repository->reveal());
    }

    /**
     * @test
     * @expectedException \Yokai\SecurityTokenBundle\Exception\TokenNotFoundException
     */
    public function it_throw_exception_if_token_not_found()
    {
        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     * @expectedException \Yokai\SecurityTokenBundle\Exception\TokenExpiredException
     */
    public function it_throw_exception_if_token_expired()
    {
        $token = new Token('string', 'jdoe','unique', 'init_password',  '-1 day', []);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     * @expectedException \Yokai\SecurityTokenBundle\Exception\TokenUsedException
     */
    public function it_throw_exception_if_token_used()
    {
        $token = new Token('string', 'jdoe','unique', 'init_password',  '+1 day', []);
        $token->setUsedAt(new \DateTime());
        $token->setUsedInformation(['info']);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $this->repository()->get('unique', 'init_password');
    }

    /**
     * @test
     */
    public function it_get_valid_token()
    {
        $token = new Token('string', 'jdoe','unique', 'init_password',  '+1 day', []);

        $this->repository->findOneBy(['value' => 'unique', 'purpose' => 'init_password'])
            ->shouldBeCalledTimes(1)
            ->willReturn($token);

        $got = $this->repository()->get('unique', 'init_password');

        self::assertSame($token, $got);
    }

    /**
     * @test
     */
    public function it_create_token()
    {
        $token = new Token('string', 'jdoe','unique', 'init_password',  '+1 day', []);

        $this->manager->persist($token)
            ->shouldBeCalledTimes(1);
        $this->manager->flush($token)
            ->shouldBeCalledTimes(1);

        $this->repository()->create($token);
    }

    /**
     * @test
     */
    public function it_update_token()
    {
        $token = new Token('string', 'jdoe','unique', 'init_password',  '+1 day', []);

        $this->manager->persist($token)
            ->shouldBeCalledTimes(1);
        $this->manager->flush($token)
            ->shouldBeCalledTimes(1);

        $this->repository()->update($token);
    }
}
