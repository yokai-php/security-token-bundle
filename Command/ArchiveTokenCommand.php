<?php

namespace Yokai\SecurityTokenBundle\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ArchiveTokenCommand extends Command
{
    /**
     * @var ArchivistInterface
     */
    private $archivist;

    /**
     * @param ArchivistInterface $archivist
     */
    public function __construct(ArchivistInterface $archivist)
    {
        $this->archivist = $archivist;

        parent::__construct('yokai:security-token:archive');
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->addOption('purpose', null, InputOption::VALUE_OPTIONAL, 'Filter tokens to archive on purpose.')
            ->addOption('before',  null, InputOption::VALUE_OPTIONAL, 'Filter tokens to archive on created date.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $questionHelper QuestionHelper */
        $questionHelper = $this->getHelperSet()->get('question');

        $purpose = $input->getOption('purpose');
        $before = $input->getOption('before');

        if ($input->isInteractive() && !$before) {
            $question = new ConfirmationQuestion(
                '<question>Careful, all consumed security token will be removed. Do you want to continue y/N ?</question>',
                false
            );

            if (!$questionHelper->ask($input, $output, $question)) {
                return;
            }
        }

        $beforeDate = null;
        if ($before) {
            $beforeDate = (new DateTime())->modify('-'.$before);
        }

        $count = $this->archivist->archive($purpose, $beforeDate);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );
    }
}
