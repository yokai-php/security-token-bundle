<?php

namespace Yokai\SecurityTokenBundle\Tests\Command;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ArchiveTokenCommandTest extends KernelTestCase
{
    /**
     * @var ArchivistInterface|ObjectProphecy
     */
    private $archivist;

    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->archivist = $this->prophesize(ArchivistInterface::class);

        self::bootKernel();
        self::$kernel->getContainer()->set('yokai_security_token.archivist', $this->archivist->reveal());

        $this->application = new Application(self::$kernel);
    }

    protected function tearDown()
    {
        parent::tearDown();

        unset(
            $this->archivist,
            $this->application
        );
    }

    protected function command()
    {
        return $this->application->find('yokai:security-token:archive');
    }

    protected function runCommand(Command $command, array $options = [])
    {
        $input = ['command' => $command->getName()];
        foreach ($options as $name => $value) {
            $input['--'.$name] = $value;
        }
        $tester = new CommandTester($command);
        $tester->execute($input);

        return $tester->getDisplay();
    }

    /**
     * @test
     */
    public function it_archive_no_token_when_run_without_options_without_confirmation()
    {
        $command = $this->command();

        $question = $this->createMock(QuestionHelper::class);
        $question->expects($this->once())
            ->method('ask')
            ->willReturn(false);

        $command->getHelperSet()->set($question, 'question');

        $this->archivist->archive(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->runCommand($command);
    }

    /**
     * @test
     */
    public function it_archive_every_token_when_run_without_options_with_confirmation()
    {
        $command = $this->command();

        $question = $this->createMock(QuestionHelper::class);
        $question->expects($this->once())
            ->method('ask')
            ->willReturn(true);

        $command->getHelperSet()->set($question, 'question');

        $this->archivist->archive(null, null)
            ->shouldBeCalledTimes(1)
            ->willReturn(10);

        $output = $this->runCommand($command);

        self::assertContains('Successfully archived 10 security token(s).', $output);
    }

    /**
     * @test
     */
    public function it_archive_partial_tokens_when_run_with_options()
    {
        $command = $this->command();

        $question = $this->createMock(QuestionHelper::class);
        $question->expects($this->never())
            ->method('ask');

        $command->getHelperSet()->set($question, 'question');

        $dateAssertions = Argument::allOf(
            Argument::type(\DateTime::class),
            Argument::that(function (\DateTime $date) {
                return $date->format('Y') === (string) (date('Y') - 1);
            })
        );

        $this->archivist->archive('init_password', $dateAssertions)
            ->shouldBeCalledTimes(1)
            ->willReturn(10);

        $output = $this->runCommand($command, ['purpose' => 'init_password', 'before' => '1 year']);

        self::assertContains('Successfully archived 10 security token(s).', $output);
    }
}
