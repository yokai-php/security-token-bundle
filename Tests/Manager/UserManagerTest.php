<?php

namespace Yokai\SecurityTokenBundle\Tests\Manager;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Yokai\SecurityTokenBundle\Manager\UserManager;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManagerInterface|ObjectProphecy
     */
    private $entityManager;

    /**
     * @var ClassMetadata|ObjectProphecy
     */
    private $classMetadata;

    protected function setUp()
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->classMetadata = $this->prophesize(ClassMetadata::class);
    }

    protected function tearDown()
    {
        unset(
            $this->entityManager,
            $this->classMetadata
        );
    }

    protected function manager()
    {
        return new UserManager($this->entityManager->reveal());
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
    public function it_get_user()
    {
        $expected = $this->user('jdoe');

        $this->entityManager->find(get_class($expected), 'jdoe')
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

        $this->entityManager->getClassMetadata(get_class($expected))
            ->shouldBeCalledTimes(1)
            ->willReturn($this->classMetadata->reveal());

        $this->classMetadata->getIdentifierValues($expected)
            ->shouldBeCalledTimes(1)
            ->willReturn(['id' => 'jdoe']);

        $id = $this->manager()->getId($expected);

        self::assertSame('jdoe', $id);
    }
}
