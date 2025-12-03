<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Manager\ChainUserManager;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Tests\Manager\Mock\UserDocument;
use Yokai\SecurityTokenBundle\Tests\Manager\Mock\UserEntity;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class ChainUserManagerTest extends TestCase
{
    private function manager($managers): ChainUserManager
    {
        return new ChainUserManager($managers);
    }

    private function entityManager(): UserManagerInterface
    {
        /** @var MockObject<UserManagerInterface> $manager */
        $manager = $this->createMock(UserManagerInterface::class);
        $manager->method('supportsClass')
            ->willReturnCallback(function ($class) {
                return $class === UserEntity::class;
            });
        $manager->method('supportsUser')
            ->willReturnCallback(function ($object) {
                return $object instanceof UserEntity;
            });
        $manager->method('getClass')
            ->with(self::isInstanceOf(UserEntity::class))
            ->willReturn(UserEntity::class);
        $manager->method('getId')
            ->willReturn('increment');
        $manager->method('get')
            ->with(
                UserEntity::class,
                \method_exists(self::class, 'isString') ? self::isString() : self::isType('string'),
            )
            ->willReturn(new UserEntity());

        return $manager;
    }

    private function documentManager(): UserManagerInterface
    {
        /** @var MockObject<UserManagerInterface> $manager */
        $manager = $this->createMock(UserManagerInterface::class);
        $manager->method('supportsClass')
            ->willReturnCallback(function ($class) {
                return $class === UserDocument::class;
            });
        $manager->method('supportsUser')
            ->willReturnCallback(function ($object) {
                return $object instanceof UserDocument;
            });
        $manager->method('getClass')
            ->with(self::isInstanceOf(UserDocument::class))
            ->willReturn(UserDocument::class);
        $manager->method('getId')
            ->willReturn('uuid');
        $manager->method('get')
            ->with(
                UserDocument::class,
                \method_exists(self::class, 'isString') ? self::isString() : self::isType('string'),
            )
            ->willReturn(new UserDocument());

        return $manager;
    }

    public function test_it_supports_same_classes_as_managers(): void
    {
        $entityManager = $this->entityManager();
        $documentManager = $this->documentManager();

        $entity = UserEntity::class;
        $document = UserDocument::class;

        $userEntityManagerAlias = $this->manager([$entityManager]);
        self::assertTrue($userEntityManagerAlias->supportsClass($entity));
        self::assertFalse($userEntityManagerAlias->supportsClass($document));

        $userDocumentManagerAlias = $this->manager([$documentManager]);
        self::assertFalse($userDocumentManagerAlias->supportsClass($entity));
        self::assertTrue($userDocumentManagerAlias->supportsClass($document));

        $userCompleteManager = $this->manager([$entityManager, $documentManager]);
        self::assertTrue($userCompleteManager->supportsClass($entity));
        self::assertTrue($userCompleteManager->supportsClass($document));

        $userEmptyManager = $this->manager([]);
        self::assertFalse($userEmptyManager->supportsClass($entity));
        self::assertFalse($userEmptyManager->supportsClass($document));
    }

    public function test_it_supports_same_users_as_managers(): void
    {
        $entityManager = $this->entityManager();
        $documentManager = $this->documentManager();

        $entity = new UserEntity();
        $document = new UserDocument();

        $userEntityManagerAlias = $this->manager([$entityManager]);
        self::assertTrue($userEntityManagerAlias->supportsUser($entity));
        self::assertFalse($userEntityManagerAlias->supportsUser($document));

        $userDocumentManagerAlias = $this->manager([$documentManager]);
        self::assertFalse($userDocumentManagerAlias->supportsUser($entity));
        self::assertTrue($userDocumentManagerAlias->supportsUser($document));

        $userCompleteManager = $this->manager([$entityManager, $documentManager]);
        self::assertTrue($userCompleteManager->supportsUser($entity));
        self::assertTrue($userCompleteManager->supportsUser($document));

        $userEmptyManager = $this->manager([]);
        self::assertFalse($userEmptyManager->supportsUser($entity));
        self::assertFalse($userEmptyManager->supportsUser($document));
    }

    public function test_it_get_user_class_from_appropriate_manager(): void
    {
        $entity = new UserEntity();
        $document = new UserDocument();

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertSame(UserEntity::class, $userCompleteManager->getClass($entity));
        self::assertSame(UserDocument::class, $userCompleteManager->getClass($document));
    }

    public function test_it_get_user_id_from_appropriate_manager()
    {
        $entity = new UserEntity();
        $document = new UserDocument();

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertSame('increment', $userCompleteManager->getId($entity));
        self::assertSame('uuid', $userCompleteManager->getId($document));
    }

    public function test_it_get_user_from_appropriate_manager(): void
    {
        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertInstanceOf(UserEntity::class, $userCompleteManager->get(UserEntity::class, '9999'));
        self::assertInstanceOf(
            UserDocument::class,
            $userCompleteManager->get(UserDocument::class, '1111-2222-3333-4444'),
        );
    }

    public function test_it_throw_exception_on_get_user_class_without_appropriate_manager(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->getClass(new \stdClass());
    }

    public function test_it_throw_exception_on_get_user_id_without_appropriate_manager(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->getId(new \stdClass());
    }

    public function test_it_throw_exception_on_get_user_without_appropriate_manager(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->get('stdClass', 'foo');
    }
}
