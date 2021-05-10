<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Factory;

use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Configuration\TokenConfiguration;
use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Factory\TokenFactory;
use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class TokenFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var InformationGuesserInterface|ObjectProphecy
     */
    private $informationGuesser;

    /**
     * @var UserManagerInterface|ObjectProphecy
     */
    private $userManager;

    /**
     * @var TokenRepositoryInterface|ObjectProphecy
     */
    private $repository;

    protected function setUp(): void
    {
        $this->informationGuesser = $this->prophesize(InformationGuesserInterface::class);
        $this->userManager = $this->prophesize(UserManagerInterface::class);
        $this->repository = $this->prophesize(TokenRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->informationGuesser,
            $this->userManager,
            $this->repository
        );
    }

    protected function factory(array $configuration): TokenFactory
    {
        return new TokenFactory(
            new TokenConfigurationRegistry($configuration),
            $this->informationGuesser->reveal(),
            $this->userManager->reveal(),
            $this->repository->reveal()
        );
    }

    /**
     * @test
     */
    public function it_create_token_according_to_configuration(): void
    {
        $generator1 = $this->createMock(TokenGeneratorInterface::class);
        $generator1->method('generate')
            ->will($this->onConsecutiveCalls('existtoken-1', 'uniquetoken-1'));
        $user1 = 'user-1';

        $generator2 = $this->createMock(TokenGeneratorInterface::class);
        $generator2->method('generate')
            ->will($this->onConsecutiveCalls('existtoken-2', 'uniquetoken-2'));
        $user2 = 'user-2';

        $generator3 = $this->createMock(TokenGeneratorInterface::class);
        $generator3->method('generate')
            ->will($this->onConsecutiveCalls('existtoken-3', 'uniquetoken-3'));
        $user3 = 'user-3';
        $token3FromRepository = new Token(
            'string',
            $user3,
            'uniquetoken-3',
            'test-3',
            '+2 minute',
            '+1 month',
            1,
            [],
            []
        );

        $configuration = [
            new TokenConfiguration('test-1', $generator1, '+1 minute', 1, '+1 month', false),
            new TokenConfiguration('test-2', $generator2, '+2 minute', 1, '+1 month', false),
            new TokenConfiguration('test-3', $generator3, '+2 minute', 1, '+1 month', true),
        ];

        $this->repository->findExisting('string', 'u1', 'test-1')
            ->shouldNotBeCalled();
        $this->repository->findExisting('string', 'u2', 'test-2')
            ->shouldNotBeCalled();
        $this->repository->findExisting('string', 'u3', 'test-3')
            ->shouldBeCalledTimes(1)
            ->willReturn($token3FromRepository);

        $this->repository->exists('existtoken-1', 'test-1')
            ->shouldBeCalledTimes(1)
            ->willReturn(true);
        $this->repository->exists('uniquetoken-1', 'test-1')
            ->shouldBeCalledTimes(1)
            ->willReturn(false);
        $this->repository->exists('existtoken-2', 'test-2')
            ->shouldBeCalledTimes(1)
            ->willReturn(true);
        $this->repository->exists('uniquetoken-2', 'test-2')
            ->shouldBeCalledTimes(1)
            ->willReturn(false);
        $this->repository->exists('existtoken-3', 'test-3')
            ->shouldNotBeCalled();
        $this->repository->exists('uniquetoken-3', 'test-3')
            ->shouldNotBeCalled();

        $this->userManager->getClass($user1)
            ->shouldBeCalledTimes(1)
            ->willReturn('string');
        $this->userManager->getClass($user2)
            ->shouldBeCalledTimes(1)
            ->willReturn('string');
        $this->userManager->getClass($user3)
            ->shouldBeCalledTimes(1)
            ->willReturn('string');

        $this->userManager->getId($user1)
            ->shouldBeCalledTimes(1)
            ->willReturn('u1');
        $this->userManager->getId($user2)
            ->shouldBeCalledTimes(1)
            ->willReturn('u2');
        $this->userManager->getId($user3)
            ->shouldBeCalledTimes(1)
            ->willReturn('u3');

        $this->informationGuesser->get()
            ->shouldBeCalledTimes(2)
            ->willReturn(['some', 'precious', 'information']);

        $token1 = $this->factory($configuration)->create($user1, 'test-1');

        self::assertInstanceOf(Token::class, $token1);
        self::assertSame('string', $token1->getUserClass());
        self::assertSame('u1', $token1->getUserId());
        self::assertSame(['some', 'precious', 'information'], $token1->getCreatedInformation());
        self::assertSame('test-1', $token1->getPurpose());
        self::assertSame([], $token1->getPayload());
        self::assertSame('uniquetoken-1', $token1->getValue());
        self::assertInstanceOf(DateTime::class, $token1->getCreatedAt());
        self::assertInstanceOf(DateTime::class, $token1->getExpiresAt());
        self::assertInstanceOf(DateTime::class, $token1->getKeepUntil());
        self::assertCount(0, $token1->getUsages());
        self::assertSame(0, $token1->getCountUsages());
        self::assertFalse($token1->isConsumed());

        $token2 = $this->factory($configuration)->create($user2, 'test-2', ['payload', 'information']);

        self::assertInstanceOf(Token::class, $token2);
        self::assertSame('string', $token2->getUserClass());
        self::assertSame('u2', $token2->getUserId());
        self::assertSame(['some', 'precious', 'information'], $token2->getCreatedInformation());
        self::assertSame('test-2', $token2->getPurpose());
        self::assertSame(['payload', 'information'], $token2->getPayload());
        self::assertSame('uniquetoken-2', $token2->getValue());
        self::assertInstanceOf(DateTime::class, $token2->getCreatedAt());
        self::assertInstanceOf(DateTime::class, $token2->getExpiresAt());
        self::assertInstanceOf(DateTime::class, $token2->getKeepUntil());
        self::assertCount(0, $token2->getUsages());
        self::assertSame(0, $token2->getCountUsages());
        self::assertFalse($token1->isConsumed());

        $token3 = $this->factory($configuration)->create($user3, 'test-3', ['payload', 'information']);

        self::assertSame($token3FromRepository, $token3);
    }
}
