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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
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
class HistoryCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setDescription('Run QATracker on your git history')
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
                name: 'step',
                mode: InputOption::VALUE_REQUIRED,
                default: 7
            );
    }


    /**
     * @param ConsoleOutput $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // eval "$(ssh-agent -s)" && ssh-add ~/.ssh/id_ed25519_docker
        $io = new SymfonyStyle($input, $output);
        $io->title('Track QA on project history');

        $fs = new Filesystem();
        $finder = new Finder();
        $projectTmpDirPath = Configuration::projectTmpDir();
        $fs->remove($projectTmpDirPath);
        $fs->mkdir($projectTmpDirPath);
        $projectDirFinder = $finder->in($projectTmpDirPath);

        $analyzeCommand = $this->getApplication()->get('analyze');
        $trackCommand = $this->getApplication()->get('track');
        $reportCommand = $this->getApplication()->get('report');

        $git = new Git();
        $repo = $git->open(Root::external());
        $remoteUrl = $repo->execute(explode(' ', 'config --get remote.origin.url'));
        $remoteUrl = reset($remoteUrl);

        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->write('Cloning repository in ' . $projectTmpDirPath . '...');
        if ($projectDirFinder->hasResults()) {
            $repo = $git->open($projectTmpDirPath);
        } else {
            $repo = $git->cloneRepository($remoteUrl, $projectTmpDirPath);
        }
        $section->writeln(self::OUTPUT_DONE);
        $io->newLine();

        $now = $input->getArgument('to') ? new DateTimeImmutable($input->getArgument('to')) : new DateTimeImmutable();
        $step = $input->getOption('step');
        $date = new DateTimeImmutable($input->getArgument('from'));
        $reset = true;

        while ($date <= $now) {
            /** @var ConsoleSectionOutput $dateSection */
            $dateSection = $output->section();

            $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::yellow('checking out...'));
            $this->checkoutByDate($date, $repo, $projectTmpDirPath);

            $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::yellow('analyzing with tools...'));
            $result = $analyzeCommand->run(new ArrayInput([]), new NullOutput());
            if ($result !== Command::SUCCESS) {
                $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::yellow('analyzing with tools, skiped...'));
                $date = $date->add(new DateInterval('P7D'));
                continue;
            }

            $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::yellow('tracking metrics...'));
            $subOutput = clone $output;
            $subOutput->setVerbosity(Output::VERBOSITY_SILENT);
            $result = $trackCommand->run(new ArrayInput(
                ['--date' => $date->format('YmdHis')]
                + ($reset ? ['--reset-data-series' => true] : [])
            ), $subOutput);
            if ($result !== Command::SUCCESS) {
                $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::yellow('tracking metrics, skiped...'));
                $date = $date->add(new DateInterval("P{$step}D"));
                continue;
            }

            $dateSection->overwrite($date->format('Y-m-d H:i:s') . ': ' . self::OUTPUT_DONE);

            $date = $date->add(new DateInterval('P7D'));
            $reset = false;
        }

        $io->newLine();

        /** @var ConsoleSectionOutput $section */
        $section = $output->section();
        $section->write('Generating report: ' . self::yellow('processing...'));

        $subOutput = clone $output;
        $subOutput->setVerbosity(Output::VERBOSITY_SILENT);
        $result = $reportCommand->run(new ArrayInput([]), $subOutput);
        if ($result !== Command::SUCCESS) {
            $io->newLine();
            $io->error("Unable to generate report :-(");
            return Command::FAILURE;
        }

        $section->overwrite('Generating report: ' . self::OUTPUT_DONE);

        $io->newLine();
        $io->success("Well done ! QA history on your project is now available !");

        return Command::SUCCESS;
    }

    public function checkoutByDate(DateTimeImmutable $date, GitRepository $repo, string $workdirPath): void
    {
        $commitByDate = $date->format('Y-m-d H:i:s');
        $commitRefByDateCommand = 'git rev-list -n 1 --first-parent --before="' . $commitByDate . '" main';
        $commitRefByDate = trim(Process::fromShellCommandline($commitRefByDateCommand, $workdirPath)->mustRun()->getOutput());
        $repo->checkout($commitRefByDate);
    }

}
