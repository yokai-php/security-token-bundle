<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Integration;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;
use Yokai\SecurityTokenBundle\Entity\Token;
use Yokai\SecurityTokenBundle\Entity\TokenUsage;
use Yokai\SecurityTokenBundle\Repository\TokenRepositoryInterface;

final class DoctrineORMTokenPurgeTest extends KernelTestCase
{
    private EntityManagerInterface $manager;
    private TokenRepositoryInterface $repository;
    private ArchivistInterface $archivist;

    protected function setUp(): void
    {
        $container = self::bootKernel()->getContainer();
        $this->manager = $container->get('doctrine')->getManager();
        $this->repository = $container->get('yokai_security_token.token_repository');
        $this->archivist = $container->get('yokai_security_token.archivist');
        (new SchemaTool($this->manager))->createSchema($this->manager->getMetadataFactory()->getAllMetadata());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    public function testPurgeRemovesTokensKeptUntilBeforeTheGivenDate(): void
    {
        $this->saveToken('outdated', 'reset_password', keepUntil: '-1 day');
        $this->saveToken('kept', 'reset_password', keepUntil: '+1 day');

        self::assertSame(1, $this->repository->purge(new DateTime()));
        self::assertSame(['kept'], $this->remainingValues());
    }

    public function testPurgeCanBeRestrictedToOnePurpose(): void
    {
        $this->saveToken('outdated_reset', 'reset_password', keepUntil: '-1 day');
        $this->saveToken('outdated_verify', 'verify_email', keepUntil: '-1 day');

        self::assertSame(1, $this->repository->purge(new DateTime(), 'reset_password'));
        self::assertSame(['outdated_verify'], $this->remainingValues());
    }

    public function testPurgeRemovesUsagesOfPurgedTokens(): void
    {
        $this->saveToken('outdated', 'reset_password', keepUntil: '-1 day', consumed: true);
        $this->saveToken('kept', 'reset_password', keepUntil: '+1 day', consumed: true);

        $this->repository->purge(new DateTime());

        self::assertSame(1, $this->manager->getRepository(TokenUsage::class)->count([]));
    }

    public function testArchivistRemovesOutdatedTokensWithTheirUsages(): void
    {
        $this->saveToken('outdated_reset', 'reset_password', keepUntil: '-1 day', consumed: true);
        $this->saveToken('outdated_verify', 'verify_email', keepUntil: '-1 day', consumed: true);
        $this->saveToken('kept', 'reset_password', keepUntil: '+1 day', consumed: true);

        self::assertSame(1, $this->archivist->archive('reset_password'));
        self::assertSame(['kept', 'outdated_verify'], $this->remainingValues());
        self::assertSame(2, $this->manager->getRepository(TokenUsage::class)->count([]));

        self::assertSame(1, $this->archivist->archive());
        self::assertSame(['kept'], $this->remainingValues());
        self::assertSame(1, $this->manager->getRepository(TokenUsage::class)->count([]));
    }

    private function saveToken(string $value, string $purpose, string $keepUntil, bool $consumed = false): void
    {
        // keepUntil = expiresAt + keepDuration: expire the token now, keep it for the given offset
        $token = new Token('App\\Entity\\User', '42', $value, $purpose, 'now', $keepUntil);
        if ($consumed) {
            $token->consume([]);
        }
        $this->manager->persist($token);
        $this->manager->flush();
        $this->manager->clear();
    }

    /**
     * @return list<string>
     */
    private function remainingValues(): array
    {
        $values = \array_map(
            static fn(Token $token): string => $token->getValue(),
            $this->manager->getRepository(Token::class)->findAll(),
        );
        \sort($values);

        return $values;
    }
}
