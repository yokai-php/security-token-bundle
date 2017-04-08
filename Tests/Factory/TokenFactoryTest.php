<?php

namespace Yokai\SecurityTokenBundle\Tests\Factory;

use DateTime;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Configuration\TokenConfiguration;
use Yokai\SecurityTokenBundle\Configuration\TokenConfigurationRegistry;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Factory\TokenFactory;
use Yokai\SecurityTokenBundle\Generator\TokenGeneratorInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
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
        $this->informationGuesser = $this->prophesize(InformationGuesserInterface::class);
        $this->userManager = $this->prophesize(UserManagerInterface::class);
    }

    protected function tearDown()
    {
        unset(
            $this->informationGuesser,
            $this->userManager
        );
    }

    protected function factory(array $configuration)
    {
        return new TokenFactory(
            new TokenConfigurationRegistry($configuration),
            $this->informationGuesser->reveal(),
            $this->userManager->reveal()
        );
    }

    /**
     * @test
     */
    public function it_create_token_according_to_configuration()
    {
        /** @var TokenGeneratorInterface|ObjectProphecy $generator */
        $generator1 = $this->prophesize(TokenGeneratorInterface::class);
        $generator1->generate()
            ->shouldBeCalledTimes(1)
            ->willReturn('uniquetoken-1');
        $user1 = 'user-1';

        /** @var TokenGeneratorInterface|ObjectProphecy $generator */
        $generator2 = $this->prophesize(TokenGeneratorInterface::class);
        $generator2->generate()
            ->shouldBeCalledTimes(1)
            ->willReturn('uniquetoken-2');
        $user2 = 'user-2';

        $configuration = [
            new TokenConfiguration('test-1', $generator1->reveal(), '+1 minute', 1, '+1 month'),
            new TokenConfiguration('test-2', $generator2->reveal(), '+2 minute', 1, '+1 month'),
        ];

        $this->userManager->getClass($user1)
            ->shouldBeCalledTimes(1)
            ->willReturn('string');
        $this->userManager->getClass($user2)
            ->shouldBeCalledTimes(1)
            ->willReturn('string');

        $this->userManager->getId($user1)
            ->shouldBeCalledTimes(1)
            ->willReturn('u1');
        $this->userManager->getId($user2)
            ->shouldBeCalledTimes(1)
            ->willReturn('u2');

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
        self::assertFalse($token1->isUsed());

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
        self::assertFalse($token1->isUsed());
    }
}
