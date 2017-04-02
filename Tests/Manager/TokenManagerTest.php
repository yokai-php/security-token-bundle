<?php

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManager;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var TokenRepositoryInterface|ObjectProphecy
     */
    private $repository;

    /**
     * @var InformationGuesserInterface|ObjectProphecy
     */
    private $informationGuesser;

    /**
     * @var UserManagerInterface|ObjectProphecy
     */
    private $userManager;

    protected function setUp()
    {
        $this->factory = $this->createMock(TokenFactoryInterface::class);
        $this->repository = $this->prophesize(TokenRepositoryInterface::class);
        $this->informationGuesser = $this->prophesize(InformationGuesserInterface::class);
        $this->userManager = $this->prophesize(UserManagerInterface::class);
    }

    protected function tearDown()
    {
        unset(
            $this->factory,
            $this->repository,
            $this->informationGuesser,
            $this->userManager
        );
    }

    protected function manager()
    {
        return new TokenManager(
            $this->factory,
            $this->repository->reveal(),
            $this->informationGuesser->reveal(),
            $this->userManager->reveal()
        );
    }

    /**
     * @test
     */
    public function it_get_token_from_repository()
    {
        $this->repository->get('unique-token', 'forgot_password')
            ->shouldBeCalledTimes(1)
            ->willReturn($expected = $this->prophesize(Token::class)->reveal());

        $token = $this->manager()->get('forgot_password', 'unique-token');

        self::assertSame($expected, $token);
    }

    /**
     * @test
     */
    public function it_create_unique_token()
    {
        $token1 = new Token(
            'string',
            'jdoe',
            'unique-token-1',
            'reset-password',
            '+1 day',
            ['payload', 'information'],
            []
        );
        $token2 = new Token(
            'string',
            'jdoe',
            'unique-token-2',
            'reset-password',
            '+1 day',
            ['payload', 'information'],
            ['created', 'information']
        );

        $this->factory->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls($token1, $token2));

        $this->repository->exists('unique-token-1', 'forgot_password')
            ->shouldBeCalledTimes(1)
            ->willReturn(true);
        $this->repository->exists('unique-token-2', 'forgot_password')
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        $this->repository->create($token1)
            ->shouldNotBeCalled();
        $this->repository->create($token2)
            ->shouldBeCalledTimes(1);

        $token = $this->manager()->create('forgot_password', 'john-doe', ['payload', 'information']);

        self::assertSame($token2, $token);
    }

    /**
     * @test
     */
    public function it_consume_token()
    {
        $token = new Token('string', 'jdoe','unique-token', 'reset-password',  '+1 day', []);

        $this->informationGuesser->get()
            ->shouldBeCalledTimes(1)
            ->willReturn(['some', 'precious', 'information']);

        $this->repository->update($token)
            ->shouldBeCalledTimes(1);

        $this->manager()->setUsed($token);

        self::assertSame(['some', 'precious', 'information'], $token->getUsedInformation());
        self::assertInstanceOf(\DateTime::class, $token->getUsedAt());
    }

    /**
     * @test
     */
    public function it_extract_user_from_token()
    {
        $token = new Token('string', 'jdoe','unique-token', 'reset-password',  '+1 day', []);

        $this->userManager->get('string', 'jdoe')
            ->shouldBeCalledTimes(1)
            ->willReturn('john doe');

        $user = $this->manager()->getUser($token);

        self::assertSame('john doe', $user);
    }
}
