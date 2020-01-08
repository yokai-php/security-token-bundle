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
    protected function configure()
    {
        $this
            ->setName('yokai:security-token:archive')
            ->addOption('purpose', null, InputOption::VALUE_OPTIONAL, 'Filter tokens to archive on purpose.')
            ->addOption('before',  null, InputOption::VALUE_OPTIONAL, '[deprecated] Filter tokens to archive on created date.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $purpose = $input->getOption('purpose');

        if ($input->getOption('before')) {
            @trigger_error(
                'The "before" option is deprecated since version 2.2 and will be removed in 3.0.',
                E_USER_DEPRECATED
            );
        }

        $count = $this->archivist->archive($purpose);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );
    }
}
