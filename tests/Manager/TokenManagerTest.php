<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Event\ConsumeTokenEvent;
use Yokai\SecurityTokenBundle\Event\CreateTokenEvent;
use Yokai\SecurityTokenBundle\Event\TokenAlreadyConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenConsumedEvent;
use Yokai\SecurityTokenBundle\Event\TokenCreatedEvent;
use Yokai\SecurityTokenBundle\Event\TokenExpiredEvent;
use Yokai\SecurityTokenBundle\Event\TokenNotFoundEvent;
use Yokai\SecurityTokenBundle\Event\TokenRetrievedEvent;
use Yokai\SecurityTokenBundle\Event\TokenTotallyConsumedEvent;
use Yokai\SecurityTokenBundle\EventDispatcher;
use Yokai\SecurityTokenBundle\Exception\TokenConsumedException;
use Yokai\SecurityTokenBundle\Exception\TokenExpiredException;
use Yokai\SecurityTokenBundle\Exception\TokenNotFoundException;
use Yokai\SecurityTokenBundle\Factory\TokenFactoryInterface;
use Yokai\SecurityTokenBundle\InformationGuesser\InformationGuesserInterface;
use Yokai\SecurityTokenBundle\Manager\TokenManager;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class TokenManagerTest extends TestCase
{
    /**
     * @var MockObject<TokenFactoryInterface
     */
    private $factory;

    /**
     * @var MockObject<TokenRepositoryInterface>
     */
    private $repository;

    /**
     * @var MockObject<InformationGuesserInterface>
     */
    private $informationGuesser;

    /**
     * @var MockObject<UserManagerInterface>
     */
    private $userManager;

    /**
     * @var MockObject<EventDispatcherInterface>
     */
    private $eventDispatcher;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(TokenFactoryInterface::class);
        $this->repository = $this->createMock(TokenRepositoryInterface::class);
        $this->informationGuesser = $this->createMock(InformationGuesserInterface::class);
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->factory,
            $this->repository,
            $this->informationGuesser,
            $this->userManager,
            $this->eventDispatcher,
        );
    }

    protected function manager(): TokenManager
    {
        return new TokenManager(
            $this->factory,
            $this->repository,
            $this->informationGuesser,
            $this->userManager,
            new EventDispatcher($this->eventDispatcher),
        );
    }

    public function test_it_dispatch_not_found_exceptions_on_get_token_from_repository(): void
    {
        $this->expectException(TokenNotFoundException::class);

        $this->repository->expects(self::once())
            ->method('get')
            ->with('unique-token', 'forgot_password')
            ->willThrowException(TokenNotFoundException::create('unique-token', 'forgot_password'));

        $notFoundEvent = self::callback(function ($event) {
            return $event instanceof TokenNotFoundEvent
                && $event->getPurpose() === 'forgot_password'
                && $event->getValue() === 'unique-token';
        });
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($notFoundEvent);

        $this->manager()->get('forgot_password', 'unique-token');
    }

    public function test_it_dispatch_expired_exceptions_on_get_token_from_repository(): void
    {
        $this->expectException(TokenExpiredException::class);

        $this->repository->expects(self::once())
            ->method('get')
            ->with('unique-token', 'forgot_password')
            ->willThrowException(TokenExpiredException::create('unique-token', 'forgot_password', new \DateTime()));

        $expiredEvent = self::callback(function ($event) {
            return $event instanceof TokenExpiredEvent
                && $event->getPurpose() === 'forgot_password'
                && $event->getValue() === 'unique-token';
        });
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($expiredEvent);

        $this->manager()->get('forgot_password', 'unique-token');
    }

    public function test_it_dispatch_used_exceptions_on_get_token_from_repository(): void
    {
        $this->expectException(TokenConsumedException::class);

        $this->repository->expects(self::once())
            ->method('get')
            ->with('unique-token', 'forgot_password')
            ->willThrowException(TokenConsumedException::create('unique-token', 'forgot_password', 3));

        $alreadyConsumedEvent = self::callback(function ($event) {
            return $event instanceof TokenAlreadyConsumedEvent
                && $event->getPurpose() === 'forgot_password'
                && $event->getValue() === 'unique-token';
        });
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($alreadyConsumedEvent);

        $this->manager()->get('forgot_password', 'unique-token');
    }

    public function test_it_get_token_from_repository(): void
    {
        $this->repository->expects(self::once())
            ->method('get')
            ->with('unique-token', 'forgot_password')
            ->willReturn($expected = $this->createMock(Token::class));

        $retrievedEvent = self::callback(function ($event) use ($expected) {
            return $event instanceof TokenRetrievedEvent
                && $event->getToken() === $expected;
        });
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($retrievedEvent);

        $token = $this->manager()->get('forgot_password', 'unique-token');

        self::assertSame($expected, $token);
    }

    public function test_it_create_unique_token(): void
    {
        $expectedToken = new Token(
            'string',
            'jdoe',
            'unique-token-2',
            'forgot_password',
            '+1 day',
            '+1 month',
            1,
            ['payload', 'information'],
            ['created', 'information'],
        );

        $this->factory->expects(self::once())
            ->method('create')
            ->with('john-doe', 'forgot_password', ['payload', 'information'])
            ->willReturn($expectedToken);

        $this->repository->expects(self::once())
            ->method('create')
            ->with($expectedToken);

        $events = self::callback(function ($event) use ($expectedToken) {
            $isCreateTokenEvent = $event instanceof CreateTokenEvent
                && $event->getPurpose() === 'forgot_password'
                && $event->getUser() === 'john-doe'
                && $event->getPayload() === ['payload', 'information'];
            $isCreatedTokenEvent = $event instanceof TokenCreatedEvent
                && $event->getToken() === $expectedToken;

            return $isCreateTokenEvent || $isCreatedTokenEvent;
        });
        $this->eventDispatcher->expects(self::exactly(2))
            ->method('dispatch')
            ->with($events);

        $token = $this->manager()->create('forgot_password', 'john-doe', ['payload', 'information']);

        self::assertSame($expectedToken, $token);
    }

    public function test_it_consume_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique-token', 'reset-password', '+1 day', '+1 month');

        $this->informationGuesser->expects(self::once())
            ->method('get')
            ->willReturn(['some', 'precious', 'information']);

        $this->repository->expects(self::once())
            ->method('update')
            ->with($token);

        $events = self::callback(function ($event) use ($token) {
            $isConsumeEvent = $event instanceof ConsumeTokenEvent
                && $event->getToken() === $token
                && $event->getInformation() === ['some', 'precious', 'information'];
            $isConsumedEvent = $event instanceof TokenConsumedEvent
                && $event->getToken() === $token;
            $isTotallyConsumedEvent = $event instanceof TokenTotallyConsumedEvent
                && $event->getToken() === $token;

            return $isConsumeEvent || $isConsumedEvent || $isTotallyConsumedEvent;
        });
        $this->eventDispatcher->expects(self::exactly(3))
            ->method('dispatch')
            ->with($events);

        $this->manager()->consume($token);

        self::assertCount(1, $token->getUsages());
        self::assertSame(1, $token->getCountUsages());
        self::assertTrue($token->isConsumed());
        self::assertNotNull($usage = $token->getLastUsage());
        self::assertSame(['some', 'precious', 'information'], $usage->getInformation());
        self::assertInstanceOf(\DateTime::class, $usage->getCreatedAt());
    }

    public function test_it_extract_user_from_token(): void
    {
        $token = new Token('string', 'jdoe', 'unique-token', 'reset-password', '+1 day', '+1 month', 1, []);

        $this->userManager->expects(self::once())
            ->method('get')
            ->with('string', 'jdoe')
            ->willReturn('john doe');

        $user = $this->manager()->getUser($token);

        self::assertSame('john doe', $user);
    }
}
