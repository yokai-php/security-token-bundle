<?php

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Manager\DoctrineUserManager;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
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

    protected function setUp()
    {
        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->objectManager = $this->prophesize(ObjectManager::class);
        $this->classMetadata = $this->prophesize(ClassMetadata::class);
    }

    protected function tearDown()
    {
        unset(
            $this->registry,
            $this->objectManager,
            $this->classMetadata
        );
    }

    protected function manager()
    {
        return new DoctrineUserManager($this->registry->reveal());
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

    /**
     * @test
     */
    public function it_supports_doctrine_entities()
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
    public function it_do_not_supports_objects_out_of_doctrine()
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
    public function it_get_user()
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
    public function it_get_user_class()
    {
        $expected = $this->user('jdoe');

        $class = $this->manager()->getClass($expected);

        self::assertSame(get_class($expected), $class);
    }

    /**
     * @test
     */
    public function it_get_user_id()
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
