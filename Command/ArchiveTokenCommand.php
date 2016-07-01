<?php

namespace Yokai\SecurityTokenBundle\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Yokai\SecurityTokenBundle\Archive\ArchivistInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ArchiveTokenCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

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

        /** @var $archivist ArchivistInterface */
        $archivist = $this->container->get('yokai_security_token.resolved.archivist');

        $count = $archivist->archive($purpose, $beforeDate);

        $output->writeln(
            sprintf('<info>Successfully archived <comment>%d</comment> security token(s).</info>', $count)
        );
    }
}
