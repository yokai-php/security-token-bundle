<?php

declare(strict_types=1);

namespace Yokai\SecurityTokenBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ArchiveTokenCommand extends Command
{
    /**
     * @var ArchivistInterface
     */
    private $archivist;

    public function __construct(ArchivistInterface $archivist)
    {
        $this->archivist = $archivist;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('yokai:security-token:archive')
            ->addOption('purpose', null, InputOption::VALUE_OPTIONAL, 'Filter tokens to archive on purpose.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $purpose */
        $purpose = $input->getOption('purpose');

        $count = $this->archivist->archive($purpose);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );

        return 0;
    }
}
