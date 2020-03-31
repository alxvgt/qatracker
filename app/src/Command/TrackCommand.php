<?php

namespace App\Command;

use App\Chart\ChartGenerator;
use App\Configuration\ConfigurationLoader;
use App\DataSerie\DataSerie;
use App\Twig\TwigFactory;
use Goat1000\SVGGraph\LineGraph;
use Goat1000\SVGGraph\SVGGraph;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TrackCommand extends Command
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;
    public const BASE_DIR = '.qatracker';
    public const OUTPUT_DIR = 'output';
    public const OUTPUT_FILENAME = 'index.html';
    public const DEFAULT_TEMPLATE = 'index.html.twig';
    public const CONFIG_FILENAME = 'config.yaml';

    protected static $defaultName = 'track';

    protected function configure()
    {
        $outputDir = static::getBaseDir().'/'.static::OUTPUT_DIR;

        $this
            ->setDescription('Track your QA indicators')
            ->setHelp('This command allows you to fetch some indicators and build simple QA chats...')
            ->addOption('no-report', null, InputOption::VALUE_NONE, 'Do not execute the report rendering step')
            ->addOption('no-track', null, InputOption::VALUE_NONE, 'Do not execute the tracking step')
            ->addOption('report-html-path', null, InputOption::VALUE_OPTIONAL,
                'Define a custom path for the html report',
                $outputDir.'/'.static::OUTPUT_FILENAME)
            ->addOption('config-path', null, InputOption::VALUE_OPTIONAL,
                'Define a custom path for the configuration file',
                static::getBaseDir().'/'.static::CONFIG_FILENAME);
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
            $configPath = $input->getOption('config-path');
            $config = ConfigurationLoader::load($configPath);
            $io->text('done.');
            $io->newLine();

            $noTrack = $input->getOption('no-track');
            if (!$noTrack) {
                $series = $config['qatracker']['series'];
                $graphs = $this->trackDataSeries($series, $io);
            }

            $noReport = $input->getOption('no-report');
            if (!$noReport) {
                $io->text('Rendering report...');
                $html = $twig->render(static::DEFAULT_TEMPLATE, [
                    'graphs' => $graphs,
                ]);
                file_put_contents($outputFilePath, $html);
                $io->text('done. Report generated at : '.$outputFilePath);
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
     * @param string       $name
     * @param string       $provider
     * @param array        $arguments
     * @return SVGGraph
     * @throws JsonException
     */
    protected function trackDataSerie(SymfonyStyle $io, string $name, string $provider, array $arguments): SVGGraph
    {
        $io->section(sprintf('Processing "%s" data serie', $name));

        $io->text('Collecting new indicator value...');
        $provider = new $provider(...$arguments);
        $value = $provider->fetchData();
        $io->text('done.');

        $io->text('Updating data serie...');
        $dataSerie = new DataSerie($name);
        $dataSerie->addData($value);
        $dataSerie->save();
        $io->text('done.');

        return ChartGenerator::generate($dataSerie->getData());
    }

    /**
     * @param array        $series
     * @param SymfonyStyle $io
     * @return array
     * @throws JsonException
     */
    protected function trackDataSeries(array $series, SymfonyStyle $io): array
    {
        $graphs = [];
        foreach ($series as $serie) {
            $graph = null;
            $name = $serie['name'];
            $provider = $serie['provider'];
            $arguments = $serie['arguments'];

            $graph = $this->trackDataSerie($io, $name, $provider, $arguments);
            $graphs [] = [
                'graph' => $graph->fetch(LineGraph::class),
                'js'    => $graph::fetchJavascript(),
            ];
        }

        return $graphs;
    }

    /**
     * @return string
     */
    public static function getBaseDir(): string
    {
        $baseDir = static::BASE_DIR;
        if (file_exists($baseDir)) {
            return $baseDir;
        }

        $baseDir = static::BASE_DIR.'.dist';
        if (file_exists($baseDir)) {
            return $baseDir;
        }

        throw new \RuntimeException('Unable to find base directory');
    }


}