<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Manager\DoctrineUserManager;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class DoctrineUserManagerTest extends TestCase
{
    /**
     * @var ManagerRegistry|ObjectProphecy
     */
    private $registry;

    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $objectManager;

    /**
     * @var ClassMetadata|ObjectProphecy
     */
    private $classMetadata;

    protected function setUp(): void
    {
        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->objectManager = $this->prophesize(ObjectManager::class);
        $this->classMetadata = $this->prophesize(ClassMetadata::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->registry,
            $this->objectManager,
            $this->classMetadata
        );
    }

    protected function manager(): DoctrineUserManager
    {
        return new DoctrineUserManager($this->registry->reveal());
    }

    protected function user($id)
    {
        return new class ($id) {
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

    /**
     * @test
     */
    public function it_supports_doctrine_entities(): void
    {
        $user = $this->user('jdoe');

        $this->registry->getManagerForClass(get_class($user))
            ->willReturn($this->objectManager->reveal());

        $manager = $this->manager();
        self::assertTrue($manager->supportsClass(get_class($user)));
        self::assertTrue($manager->supportsUser($user));
    }

    /**
     * @test
     */
    public function it_do_not_supports_objects_out_of_doctrine(): void
    {
        $user = $this->user('jdoe');

        $this->registry->getManagerForClass(get_class($user))
            ->willReturn(null);

        $manager = $this->manager();
        self::assertFalse($manager->supportsClass(get_class($user)));
        self::assertFalse($manager->supportsUser($user));
    }

    /**
     * @test
     */
    public function it_get_user(): void
    {
        $expected = $this->user('jdoe');

        $this->registry->getManagerForClass(get_class($expected))
            ->shouldBeCalledTimes(1)
            ->willReturn($this->objectManager->reveal());

        $this->objectManager->find(get_class($expected), 'jdoe')
            ->shouldBeCalledTimes(1)
            ->willReturn($expected);

        $user = $this->manager()->get(get_class($expected), 'jdoe');

        self::assertSame($expected, $user);
    }

    /**
     * @test
     */
    public function it_get_user_class(): void
    {
        $expected = $this->user('jdoe');

        $class = $this->manager()->getClass($expected);

        self::assertSame(get_class($expected), $class);
    }

    /**
     * @test
     */
    public function it_get_user_id(): void
    {
        $expected = $this->user('jdoe');

        $this->registry->getManagerForClass(get_class($expected))
            ->shouldBeCalledTimes(1)
            ->willReturn($this->objectManager->reveal());

        $this->objectManager->getClassMetadata(get_class($expected))
            ->shouldBeCalledTimes(1)
            ->willReturn($this->classMetadata->reveal());

        $this->classMetadata->getIdentifierValues($expected)
            ->shouldBeCalledTimes(1)
            ->willReturn(['id' => 'jdoe']);

        $id = $this->manager()->getId($expected);

        self::assertSame('jdoe', $id);
    }
}
