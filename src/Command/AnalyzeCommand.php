<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\Helper\Helper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'analyze')]
class AnalyzeCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Run QA tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);
        $io->title('Analyze QA on your project');

        foreach (Configuration::analyze() as $toolName => $commandLine) {
            $io->writeln('Processing '.(new Helper())->interpretString($commandLine));
            $process = Process::fromShellCommandline($commandLine);
            $process->setTimeout(0);
            $process->mustRun();
            $io->success($toolName.' executed.');
        }

        return Command::SUCCESS;
    }
}
