<?php

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Manager\ChainUserManager;
use Yokai\SecurityTokenBundle\Manager\UserManagerInterface;
use Yokai\SecurityTokenBundle\Tests\Manager\Mock\UserDocument;
use Yokai\SecurityTokenBundle\Tests\Manager\Mock\UserEntity;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ChainUserManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param UserManagerInterface[] $managers
     *
     * @return ChainUserManager
     */
    private function manager($managers)
    {
        return new ChainUserManager($managers);
    }

    /**
     * @return UserManagerInterface
     */
    private function entityManager()
    {
        /** @var UserManagerInterface|ObjectProphecy $manager */
        $manager = $this->prophesize(UserManagerInterface::class);

        $manager->supportsClass(UserEntity::class)
            ->willReturn(true);
        $manager->supportsClass(UserDocument::class)
            ->willReturn(false);

        $manager->supportsUser(Argument::type(UserEntity::class))
            ->willReturn(true);
        $manager->supportsUser(Argument::type(UserDocument::class))
            ->willReturn(false);

        $manager->getClass(Argument::type(UserEntity::class))
            ->willReturn(UserEntity::class);

        $manager->getId(Argument::type(UserEntity::class))
            ->willReturn('increment');

        $manager->get(UserEntity::class, Argument::type('int'))
            ->willReturn(new UserEntity());

        return $manager->reveal();
    }

    /**
     * @return UserManagerInterface
     */
    private function documentManager()
    {
        /** @var UserManagerInterface|ObjectProphecy $manager */
        $manager = $this->prophesize(UserManagerInterface::class);

        $manager->supportsClass(UserEntity::class)
            ->willReturn(false);
        $manager->supportsClass(UserDocument::class)
            ->willReturn(true);

        $manager->supportsUser(Argument::type(UserEntity::class))
            ->willReturn(false);
        $manager->supportsUser(Argument::type(UserDocument::class))
            ->willReturn(true);

        $manager->getClass(Argument::type(UserDocument::class))
            ->willReturn(UserDocument::class);

        $manager->getId(Argument::type(UserDocument::class))
            ->willReturn('uuid');

        $manager->get(UserDocument::class, Argument::type('string'))
            ->willReturn(new UserDocument());

        return $manager->reveal();
    }

    /**
     * @test
     */
    public function it_supports_same_classes_as_managers()
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

    /**
     * @test
     */
    public function it_supports_same_users_as_managers()
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

    /**
     * @test
     */
    public function it_get_user_class_from_appropriate_manager()
    {
        $entity = new UserEntity();
        $document = new UserDocument();

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertSame(UserEntity::class, $userCompleteManager->getClass($entity));
        self::assertSame(UserDocument::class, $userCompleteManager->getClass($document));
    }

    /**
     * @test
     */
    public function it_get_user_id_from_appropriate_manager()
    {
        $entity = new UserEntity();
        $document = new UserDocument();

        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertSame('increment', $userCompleteManager->getId($entity));
        self::assertSame('uuid', $userCompleteManager->getId($document));
    }

    /**
     * @test
     */
    public function it_get_user_from_appropriate_manager()
    {
        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        self::assertInstanceOf(UserEntity::class, $userCompleteManager->get(UserEntity::class, 9999));
        self::assertInstanceOf(UserDocument::class, $userCompleteManager->get(UserDocument::class, '1111-2222-3333-4444'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throw_exception_on_get_user_class_without_appropriate_manager()
    {
        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->getClass(new \stdClass());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throw_exception_on_get_user_id_without_appropriate_manager()
    {
        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->getId(new \stdClass());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throw_exception_on_get_user_without_appropriate_manager()
    {
        $userCompleteManager = $this->manager([$this->entityManager(), $this->documentManager()]);
        $userCompleteManager->get('stdClass', 'foo');
    }
}
