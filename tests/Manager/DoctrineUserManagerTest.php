<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Yokai\SecurityTokenBundle\Manager\DoctrineUserManager;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class DoctrineUserManagerTest extends TestCase
{
    /**
     * @var MockObject<ManagerRegistry>
     */
    private $registry;

    /**
     * @var MockObject<EntityManagerInterface>
     */
    private $objectManager;

    /**
     * @var MockObject<ClassMetadata>
     */
    private $classMetadata;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->registry,
            $this->objectManager,
            $this->classMetadata,
        );
    }

    protected function manager(): DoctrineUserManager
    {
        return new DoctrineUserManager($this->registry);
    }

    protected function user($id)
    {
        return new class($id) {
            private $id;

            public function __construct($id)
            {
                $this->id = $id;
            }

            public function getId()
            {
                return $this->id;
            }
        };
    }

    public function testIt_supports_doctrine_entities(): void
    {
        $user = $this->user('jdoe');

        $this->registry->method('getManagerForClass')
            ->with(\get_class($user))
            ->willReturn($this->objectManager);

        $manager = $this->manager();
        self::assertTrue($manager->supportsClass(\get_class($user)));
        self::assertTrue($manager->supportsUser($user));
    }

    public function testIt_do_not_supports_objects_out_of_doctrine(): void
    {
        $user = $this->user('jdoe');

        $this->registry->method('getManagerForClass')
            ->with(\get_class($user))
            ->willReturn(null);

        $manager = $this->manager();
        self::assertFalse($manager->supportsClass(\get_class($user)));
        self::assertFalse($manager->supportsUser($user));
    }

    public function testIt_get_user(): void
    {
        $expected = $this->user('jdoe');

        $this->registry->expects(self::once())
            ->method('getManagerForClass')
            ->with(\get_class($expected))
            ->willReturn($this->objectManager);

        $this->objectManager->expects(self::once())
            ->method('find')
            ->with(\get_class($expected), 'jdoe')
            ->willReturn($expected);

        $user = $this->manager()->get(\get_class($expected), 'jdoe');

        self::assertSame($expected, $user);
    }

    public function testIt_get_user_class(): void
    {
        $expected = $this->user('jdoe');

        $class = $this->manager()->getClass($expected);

        self::assertSame(\get_class($expected), $class);
    }

    public function testIt_get_user_id(): void
    {
        $expected = $this->user('jdoe');

        $this->registry->expects(self::once())
            ->method('getManagerForClass')
            ->with(\get_class($expected))
            ->willReturn($this->objectManager);

        $this->objectManager->expects(self::once())
            ->method('getClassMetadata')
            ->with(\get_class($expected))
            ->willReturn($this->classMetadata);

        $this->classMetadata->expects(self::once())
            ->method('getIdentifierValues')
            ->with($expected)
            ->willReturn(['id' => 'jdoe']);

        $id = $this->manager()->getId($expected);

        self::assertSame('jdoe', $id);
    }
}
