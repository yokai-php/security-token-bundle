<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Integration;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;

/**
 * DoctrineBundle auto mapping must not map token entities where the bundle did not ask for it.
 */
final class DoctrineAutoMappingTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    public function testTokenEntitiesAreNotMappedWithACustomStorage(): void
    {
        self::bootKernel(['environment' => 'custom_storage']);

        self::assertSame([], $this->mappedTokenEntities('default'));
    }

    public function testTokenEntitiesAreOnlyMappedInTheConfiguredEntityManager(): void
    {
        self::bootKernel(['environment' => 'tokens_entity_manager']);

        self::assertSame([Token::class, TokenUsage::class], $this->mappedTokenEntities('tokens'));
        self::assertSame([], $this->mappedTokenEntities('default'));
    }

    /**
     * @return list<class-string>
     */
    private function mappedTokenEntities(string $entityManager): array
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = self::getContainer()->get('doctrine');
        $classes = $doctrine->getManager($entityManager)->getMetadataFactory()->getAllMetadata();
        $names = \array_map(static fn($metadata): string => $metadata->getName(), $classes);
        $names = \array_values(\array_filter(
            $names,
            static fn(string $name): bool => \str_starts_with($name, 'Yokai\\SecurityTokenBundle\\'),
        ));
        \sort($names);

        return $names;
    }
}
