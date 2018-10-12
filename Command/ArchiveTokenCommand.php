<?php

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
        parent::__construct();

        $this->archivist = $archivist;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setName('yokai:security-token:archive')
            ->addOption('purpose', null, InputOption::VALUE_OPTIONAL, 'Filter tokens to archive on purpose.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $purpose = $input->getOption('purpose');

        $count = $this->archivist->archive($purpose);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );

        return 0;
    }
}
