<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 *
 * phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class ArchiveTokenCommandTest extends KernelTestCase
{
    /**
     * @var MockObject<ArchivistInterface>
     */
    private $archivist;

    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->archivist = $this->createMock(ArchivistInterface::class);

        self::bootKernel();
        self::$kernel->getContainer()->set('yokai_security_token.archivist', $this->archivist);

        $this->application = new Application(self::$kernel);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->archivist,
            $this->application,
        );
    }

    protected function command()
    {
        return $this->application->get('yokai:security-token:archive');
    }

    protected function runCommand(Command $command, array $options = []): string
    {
        $input = ['command' => $command->getName()];
        foreach ($options as $name => $value) {
            $input['--' . $name] = $value;
        }
        $tester = new CommandTester($command);
        $tester->execute($input);

        return $tester->getDisplay();
    }

    public function testIt_archive_every_token_when_run_without_options_with_confirmation(): void
    {
        $command = $this->command();

        $this->archivist->expects(self::once())
            ->method('archive')
            ->with(null)
            ->willReturn(10);

        $output = $this->runCommand($command);

        self::assertStringContainsString('Successfully archived 10 security token(s).', $output);
    }

    public function testIt_archive_partial_tokens_when_run_with_options(): void
    {
        $command = $this->command();

        $this->archivist->expects(self::once())
            ->method('archive')
            ->with('init_password')
            ->willReturn(10);

        $output = $this->runCommand($command, ['purpose' => 'init_password']);

        self::assertStringContainsString('Successfully archived 10 security token(s).', $output);
    }
}
