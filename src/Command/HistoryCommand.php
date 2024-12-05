<?php

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Chart\Chart;
use Alxvng\QATracker\Chart\ChartGenerator;
use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\DataProvider\Model\DataPercentSerie;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use Alxvng\QATracker\Root\Root;
use Alxvng\QATracker\Twig\TwigFactory;
use CzProject\GitPhp\Git;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Goat1000\SVGGraph\SVGGraph;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsCommand(name: 'history')]
class HistoryCommand extends Command
{
    public const WORK_DIR = Root::BASE_DIR . '/tmp';

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'each-month',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();
        $finder = new Finder();
        $workDir = $finder->in(self::WORK_DIR);

        $git = new Git();
        $repo = $git->open(Root::external());
        $remoteUrl = $repo->execute(\explode(' ', 'config --get remote.origin.url'));
        $remoteUrl = reset($remoteUrl);

        if ($workDir->hasResults()) {
            $repo = $git->open(self::WORK_DIR);
        } else {
            $repo = $git->cloneRepository($remoteUrl, self::WORK_DIR);
        }

        $firstCommitCommand = 'git rev-list --max-parents=0 HEAD | tail -n 1';
        $process = Process::fromShellCommandline($firstCommitCommand, self::WORK_DIR);
        $firstCommitRef = trim($process->mustRun()->getOutput());
        $repoStartAt = $repo->getCommit($firstCommitRef)->getDate();

        $repo->checkout($firstCommitRef);

        $now = new DateTimeImmutable();

        $date = $repoStartAt;
        (new RunCommand())->run(
            new ArrayInput([]),
            $output
        );
        (new TrackCommand())->run(
            new ArrayInput([
                '--date' => $date->format('YmdHis'),
                '--reset-data-series' => true,
            ]),
            $output
        );

        while ($date <= $now) {
            $date = $date->add(new DateInterval('P7D'));
            $commitByDate = "main@{{$date->format('YmdHis')}}";
            $repo->checkout($commitByDate);
            (new RunCommand())->run(
                new ArrayInput([]),
                $output
            );
            (new TrackCommand())->run(
                new ArrayInput([
                    '--date' => $date->format('YmdHis'),
                ]),
                $output
            );
            dd('stop');
        }

        return Command::SUCCESS;
    }

}
