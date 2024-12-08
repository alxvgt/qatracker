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
use DateTime;
use Goat1000\SVGGraph\SVGGraph;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
        $io->title('Install QA tools');

        foreach (Configuration::install() as $tool) {

            $isActionApproved = $io->ask(
                '🤔 Do you want to install ' . $tool['binName'] . ' ? [y|n]',
                'y',
                fn($response) => 'y' === $response,
            );

            if ($isActionApproved) {
                $process = new Process(['wget', '--show-progress', '-q', '-P', Configuration::tmpDir(), $tool['downloadLink']]);
                $process->run();
                $installedPath = Configuration::tmpDir() . '/' . $tool['binName'];
                $process = new Process(['chmod', '+x', $installedPath]);
                $process->run();
                $io->success($installedPath . ' installed.');
            }
        }

        return Command::SUCCESS;
    }

}
