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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'install')]
class InstallCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Install default QA tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $io->title('Install QA tools');

        foreach (Configuration::install() as $toolName => $tool) {
            $isActionApproved = $io->ask(
                '🤔 Do you want to install '.$toolName.' ? [y|n]',
                'y',
                fn ($response) => 'y' === $response,
            );

            if ($isActionApproved) {
                $process = Process::fromShellCommandline((new Helper())->interpretString($tool['installCommand']));
                $process->run();
                $logDir = $tool['logDir'] ?? null;
                if ($logDir) {
                    $fs->mkdir((new Helper())->interpretString($tool['logDir']));
                }
                $io->success($toolName.' installed.');
            }
        }

        return Command::SUCCESS;
    }
}
