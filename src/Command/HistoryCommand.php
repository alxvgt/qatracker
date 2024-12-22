<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\Root\Root;
use CzProject\GitPhp\Git;
use CzProject\GitPhp\GitRepository;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Throwable;

use function explode;

#[AsCommand(name: 'history')]
class HistoryCommand extends BaseCommand
{
    public function checkoutByDate(DateTimeImmutable $date, GitRepository $repo, string $workdirPath): void
    {
        $commitByDate = $date->format('Y-m-d H:i:s');
        $commitRefByDateCommand = 'git rev-list -n 1 --first-parent --before="'.$commitByDate.'" main';
        $commitRefByDate = trim(Process::fromShellCommandline($commitRefByDateCommand, $workdirPath)->mustRun()->getOutput());
        $repo->checkout($commitRefByDate);
    }

    public function getFirstCommitDate(GitRepository $repo, string $workdirPath): DateTimeImmutable
    {
        $commitRef = trim(Process::fromShellCommandline('git rev-list main | tail -n 4 | head -n 1', $workdirPath)->mustRun()->getOutput());

        return $repo->getCommit($commitRef)->getDate();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run QATracker on your git history')
            ->addOption(
                name: 'from',
                mode: InputArgument::REQUIRED,
                description: 'From date',
            )
            ->addOption(
                name: 'to',
                mode: InputArgument::OPTIONAL,
                description: 'To date',
            )
            ->addOption(
                name: 'step',
                mode: InputOption::VALUE_REQUIRED,
                default: 7,
            );
    }

    /**
     * @param ConsoleOutput $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);
        $io->title('Track QA on project history');

        $fs = new Filesystem();
        $finder = new Finder();
        $projectWorkDirPath = Configuration::projectWorkDir();
        $fs->remove($projectWorkDirPath);
        $fs->mkdir($projectWorkDirPath);
        $projectDirFinder = $finder->in($projectWorkDirPath);

        $analyzeCommand = $this->getApplication()->get('analyze');
        $trackCommand = $this->getApplication()->get('track');
        $reportCommand = $this->getApplication()->get('report');

        $git = new Git();
        $repo = $git->open(Root::external());
        $remoteUrl = $repo->execute(explode(' ', 'config --get remote.origin.url'));
        $remoteUrl = reset($remoteUrl);

        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->write('Cloning repository in '.$projectWorkDirPath.'...');
        if ($projectDirFinder->hasResults()) {
            $repo = $git->open($projectWorkDirPath);
        } else {
            $repo = $git->cloneRepository($remoteUrl, $projectWorkDirPath);
        }
        $section->writeln(self::OUTPUT_DONE);
        $io->newLine();

        $end = $input->getOption('to') ? new DateTimeImmutable($input->getOption('to')) : new DateTimeImmutable();
        $step = $input->getOption('step');
        $date = $input->getOption('from') ? new DateTimeImmutable($input->getOption('from')) : $this->getFirstCommitDate($repo, $projectWorkDirPath);
        $reset = true;

        while ($date <= $end) {
            /** @var ConsoleSectionOutput $dateSection */
            $dateSection = $output->section();

            $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::yellow('checking out...'));
            $this->checkoutByDate($date, $repo, $projectWorkDirPath);

            $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::yellow('analyzing with tools...'));
            $t = null;

            try {
                $result = $analyzeCommand->run(new ArrayInput([]), new NullOutput());
            } catch (Throwable $t) {
                $result = Command::FAILURE;
            }
            if (Command::SUCCESS !== $result) {
                $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::yellow('analyzing with tools, skipped... ['.$t?->getMessage().']'));
                $date = $this->getNextDate($date, $step);

                continue;
            }

            $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::yellow('tracking metrics...'));
            $t = null;

            try {
                $result = $trackCommand->run(new ArrayInput(
                    ['--date' => $date->format('YmdHis')]
                    + ($reset ? ['--reset-data-series' => true] : []),
                ), new NullOutput());
            } catch (Throwable $t) {
                $result = Command::FAILURE;
            }
            if (Command::SUCCESS !== $result) {
                $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::yellow('tracking metrics, skipped... ['.$t?->getMessage().']'));
                $date = $this->getNextDate($date, $step);

                continue;
            }

            $dateSection->overwrite($date->format('Y-m-d H:i:s').': '.self::OUTPUT_DONE);

            $date = $this->getNextDate($date, $step);
            $reset = false;
        }

        $io->newLine();

        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->write('Generating report: '.self::yellow('processing...'));

        $subOutput = clone $output;
        $subOutput->setVerbosity(Output::VERBOSITY_SILENT);
        $result = $reportCommand->run(new ArrayInput([]), $subOutput);
        if (Command::SUCCESS !== $result) {
            $io->newLine();
            $io->error('Unable to generate report :-(');

            return Command::FAILURE;
        }

        $section->overwrite('Generating report: '.self::OUTPUT_DONE);

        $io->newLine();
        $io->success('Well done ! QA history on your project is now available !');

        return Command::SUCCESS;
    }

    private function getNextDate(mixed $date, mixed $step): mixed
    {
        return $date->add(new DateInterval("P{$step}D"));
    }
}
