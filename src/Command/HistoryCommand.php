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
use CzProject\GitPhp\GitRepository;
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
use Symfony\Component\Console\Input\InputArgument;
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
use function explode;

#[AsCommand(name: 'history')]
class HistoryCommand extends Command
{
    public const WORK_DIR = Root::BASE_DIR . '/tmp';

    public function trackMetrics(DateTimeImmutable $date, $reset = false): void
    {
        $commandLine = 'vendor/alxvng/qatracker/bin/qatracker-track -vvv --date ' . $date->format('YmdHis') . ($reset ? ' --reset-data-series' : '');
        $process = Process::fromShellCommandline($commandLine);
        $process->mustRun();
    }

    public function runTools(): void
    {
        $commandLine = "vendor/alxvng/qatracker/bin/qatracker-run -vvv";
        $process = Process::fromShellCommandline(
            $commandLine,
            env: [
                'PROJECT_PATHS_PHPLOC' => sprintf("%s/src %s/tests ", self::WORK_DIR, self::WORK_DIR),
                'PROJECT_PATHS_PHPCPD' => sprintf("%s/src %s/tests ", self::WORK_DIR, self::WORK_DIR),
                'PROJECT_PATHS_PHPMD' => sprintf("%s/src,%s/tests", self::WORK_DIR, self::WORK_DIR),
                'PROJECT_DIR_PHPUNIT' => self::WORK_DIR,
            ]
        );
        $process->mustRun();
    }

    public function checkoutByDate(DateTimeImmutable $date, GitRepository $repo): void
    {
        $commitByDate = $date->format('Y-m-d H:i:s');
        $commitRefByDateCommand = 'git rev-list -n 1 --first-parent --before="' . $commitByDate . '" main';
        $commitRefByDate = trim(Process::fromShellCommandline($commitRefByDateCommand, self::WORK_DIR)->mustRun()->getOutput());
        $repo->checkout($commitRefByDate);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'from',
                mode: InputArgument::REQUIRED,
                description: 'From date',
            )
            ->addArgument(
                name: 'to',
                mode: InputArgument::OPTIONAL,
                description: 'To date',
            )
            ->addOption(
                name: 'each-month',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // eval "$(ssh-agent -s)" && ssh-add ~/.ssh/id_ed25519_docker
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();
        $finder = new Finder();
        $fs->remove(self::WORK_DIR);
        $fs->mkdir(self::WORK_DIR);
        $workDir = $finder->in(self::WORK_DIR);

        $git = new Git();
        $repo = $git->open(Root::external());
        $remoteUrl = $repo->execute(explode(' ', 'config --get remote.origin.url'));
        $remoteUrl = reset($remoteUrl);

        if ($workDir->hasResults()) {
            $repo = $git->open(self::WORK_DIR);
        } else {
            $repo = $git->cloneRepository($remoteUrl, self::WORK_DIR);
        }

        $now = $input->getArgument('to') ? new DateTimeImmutable($input->getArgument('to')) : new DateTimeImmutable();
        $date = new DateTimeImmutable($input->getArgument('from'));
        $reset = true;
        while ($date <= $now) {
            $io->writeln('Processing ' . $date->format('Y-m-d H:i:s'));

            $io->writeln('Checking out');
            $this->checkoutByDate($date, $repo);

            $io->writeln('Running tools');
            $this->runTools();

            $io->writeln('Tracking metrics');
            $this->trackMetrics($date, $reset);

            $date = $date->add(new DateInterval('P7D'));
            $reset = false;
        }

        return Command::SUCCESS;
    }

}
