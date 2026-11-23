<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;

final class DoctrineMappingTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    public function testTokenUsageAlwaysBelongsToAToken(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()->get('doctrine')->getManager();
        $joinColumn = $manager->getClassMetadata(TokenUsage::class)->getAssociationMapping('token')->joinColumns[0];

        self::assertSame('token_id', $joinColumn->name);
        self::assertFalse($joinColumn->nullable);
    }
}
