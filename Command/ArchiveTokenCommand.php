<?php

namespace Yokai\SecurityTokenBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <eugone.yann@gmail.com>
 */
class ArchiveTokenCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('yokai:security-token:archive')
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
                '<question>Careful, all consumed security token will be removed. Do you want to continue y/n ?</question>',
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

        /** @var $archivist ArchivistInterface */
        $archivist = $this->getContainer()->get('yokai_security_token.archivist');

        $count = $archivist->archive($purpose, $beforeDate);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );
    }
}
