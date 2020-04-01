<?php

namespace App\Command;

use App\Chart\ChartGenerator;
use App\Configuration\Configuration;
use App\DataSerie\DataSerie;
use App\Twig\TwigFactory;
use Goat1000\SVGGraph\SVGGraph;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TrackCommand extends Command
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;
    public const BASE_DIR = '.qatracker';
    public const GENERATED_DIR = 'generated';
    public const OUTPUT_DIR = 'report';
    public const OUTPUT_FILENAME = 'index.html';
    public const DEFAULT_TEMPLATE = 'index.html.twig';
    public const CONFIG_FILENAME = 'config.yaml';

    protected static string $baseDir = self::BASE_DIR;

    protected static $defaultName = 'track';


    protected function configure()
    {
        $outputDir = static::getGeneratedDir().'/'.static::OUTPUT_DIR;

        $this
            ->setDescription('Track your QA indicators')
            ->setHelp('This command allows you to fetch some indicators and build simple QA charts...')
            ->addOption('no-report', null, InputOption::VALUE_NONE, 'Do not execute the report rendering step')
            ->addOption('no-track', null, InputOption::VALUE_NONE, 'Do not execute the tracking step')
            ->addOption('report-html-path', null, InputOption::VALUE_OPTIONAL,
                'Define a custom path for the html report',
                $outputDir.'/'.static::OUTPUT_FILENAME)
            ->addOption('base-dir', null, InputOption::VALUE_OPTIONAL,
                'Define a custom base directory for qatracker',
                static::getBaseDir());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        static::$baseDir = (string)$input->getOption('base-dir');

        if (file_exists(static::getConfigPath())) {
            return;
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(sprintf('File %s does not exists. Do you want to create it from the sample file ? (Y/n)',
            static::getConfigPath()), true);

        if (!$helper->ask($input, $output, $question)) {
            $io->warning(sprintf('The config file has not been created'));
            exit(static::EXIT_SUCCESS);
        }

        $fs = new Filesystem();
        $fs->copy(Configuration::EXAMPLE_CONFIG_PATH, static::getConfigPath());

        $io->success(sprintf("The config file has been created at \"%s\".\nYou can now edit it to put your own configuration.",
            static::getConfigPath()));

        exit(static::EXIT_SUCCESS);
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws JsonException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $twig = TwigFactory::getTwig();
        try {

            $io->title('Track your QA indicators');

            $io->text('Initializing...');
            $outputFilePath = $input->getOption('report-html-path');
            $this->initializeOutputDir($outputFilePath);
            $config = Configuration::load(static::getConfigPath());
            $io->text('done.');
            $io->newLine();

            $withTrack = !$input->getOption('no-track');
            $withReport = !$input->getOption('no-report');
            $graphs = [];

            $series = $config['qatracker']['series'];
            foreach ($series as $serie) {

                $serie = new DataSerie($serie);

                if ($withTrack) {
                    $this->trackDataSerie($io, $serie);
                }

                if ($withReport) {
                    $settings = [
                        'graph_title' => $serie->getName(),
                    ];
                    $graphs [] = ChartGenerator::generate($serie->getData(), $settings);
                }
            }

            if (!empty($graphs)) {
                $io->section('Rendering report...');
                $html = $twig->render(static::DEFAULT_TEMPLATE, [
                    'graphs' => $graphs,
                    'js'     => SVGGraph::fetchJavascript(),
                ]);
                file_put_contents($outputFilePath, $html);
                $io->text('done.');
                $io->text('Report generated at : '.$outputFilePath);
                $io->newLine();
            }

            $io->success('Well done ! You have track new QA indicators !');

            return static::EXIT_SUCCESS;

        } catch (\RuntimeException $e) {

            $io->error($e->getMessage());

            return static::EXIT_FAILURE;
        }

    }

    /**
     * @param $outputFilePath
     */
    protected function initializeOutputDir(string $outputFilePath): void
    {
        $outputFileDir = dirname($outputFilePath);

        if (!is_dir($outputFileDir) && !mkdir($outputFileDir, 0777, true)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $outputFileDir));
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param DataSerie    $serie
     * @throws JsonException
     */
    protected function trackDataSerie(SymfonyStyle $io, DataSerie $serie): void
    {
        $io->section(sprintf('Processing "%s" data serie', $serie->getName()));

        $io->text('Collecting new indicator value...');

        $providerClass = $serie->getProvider();
        $provider = new $providerClass(...$serie->getArguments());
        $value = $provider->fetchData();
        $io->text('done.');

        $io->text('Updating data serie...');
        $serie->addData($value);
        $serie->save();
        $io->text('done.');
    }

    /**
     * @return string
     */
    public static function getBaseDir(): string
    {
        return static::$baseDir;
    }

    /**
     * @return string
     */
    public static function getGeneratedDir(): string
    {
        return static::getBaseDir().'/'.static::GENERATED_DIR;
    }

    /**
     * @return string
     */
    public static function getConfigPath(): string
    {
        return static::getBaseDir().'/'.static::CONFIG_FILENAME;
    }

}