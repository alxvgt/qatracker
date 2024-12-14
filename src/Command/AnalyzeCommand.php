<?php

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Chart\Chart;
use Alxvng\QATracker\Chart\ChartGenerator;
use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\DataProvider\Model\DataPercentSerie;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use Alxvng\QATracker\Helper\Helper;
use Alxvng\QATracker\Root\Root;
use Alxvng\QATracker\Twig\TwigFactory;
use DateTime;
use Goat1000\SVGGraph\SVGGraph;
use JsonException;
use PHPUnit\TextUI\Help;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
            $io->writeln('Processing ' . (new Helper())->interpretString($commandLine));
            $process = Process::fromShellCommandline($commandLine);
            $process->setTimeout(0);
            $process->mustRun();
            $io->success($toolName . ' executed.');
        }

        return Command::SUCCESS;
    }

}
