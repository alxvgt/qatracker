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
class InstallCommand extends Command
{
    public const INSTALL_DIR = '/tmp';

    public function installPhar(SymfonyStyle $io, array $tools): void
    {
        foreach ($tools as $toolName => $tool) {

            $isActionApproved = $io->ask(
                '🤔 Do you want to install ' . $tool['binName'] . ' ? [y|n]',
                'y',
                fn($response) => 'y' === $response,
            );

            if ($isActionApproved) {
                $process = new Process(['wget', '--show-progress', '-q', '-P', self::INSTALL_DIR, $tool['downloadLink']]);
                $process->run();
                $installedPath = self::INSTALL_DIR . '/' . $tool['binName'];
                $process = new Process(['chmod', '+x', $installedPath]);
                $process->run();
                $io->success($installedPath . ' installed.');
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tools = [
            'phploc' => [
                'downloadLink' => 'https://phar.phpunit.de/phploc.phar',
                'binName' => 'phploc.phar',
            ],
            'phpcpd' => [
                'downloadLink' => 'https://phar.phpunit.de/phpcpd.phar',
                'binName' => 'phpcpd.phar',
            ],
            'phpmd' => [
                'downloadLink' => 'https://phpmd.org/static/latest/phpmd.phar',
                'binName' => 'phpmd.phar',
            ],
            'phpmetrics' => [
                'downloadLink' => 'https://github.com/phpmetrics/PhpMetrics/raw/master/releases/phpmetrics.phar',
                'binName' => 'phpmetrics.phar',
            ],
        ];

        $this->installPhar($io, $tools);

        return Command::SUCCESS;
    }

}
