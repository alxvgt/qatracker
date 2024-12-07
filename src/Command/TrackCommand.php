<?php

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Chart\Chart;
use Alxvng\QATracker\Chart\ChartGenerator;
use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\DataProvider\Exception\FileNotFoundException;
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
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsCommand(name: 'track')]
class TrackCommand extends Command
{
    public const VERSION = 'v0.6.0';

    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;

    public const GENERATED_DIR = 'generated';
    public const REPORT_DIR = 'report';
    public const REPORT_FILENAME = 'index.html';
    public const DEFAULT_TEMPLATE = 'index.html.twig';

    protected const OUTPUT_DONE = ' <fg=green>done</>.';

    protected static DateTime $trackDate;


    public static function getGeneratedDir(): string
    {
        $dir = Root::getConfigDir() . '/' . static::GENERATED_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        return $dir;
    }


    /**
     * @param $trackDate
     */
    public static function initializeTrackDate($trackDate): void
    {
        static::$trackDate = DateTime::createFromFormat(AbstractDataSerie::DATE_FORMAT, $trackDate);

        if (!static::$trackDate) {
            throw new \RuntimeException(sprintf('Unable to create date from option with value %s', $trackDate));
        }
    }

    /**
     * @return DateTime|false
     */
    public static function getTrackDate(): DateTime|bool
    {
        return static::$trackDate;
    }

    protected static function getReportPath(): string
    {
        $path = static::getGeneratedDir() . '/' . static::REPORT_DIR . '/' . static::REPORT_FILENAME;

        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        return $path;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Track your QA indicators')
            ->setHelp('This command allows you to fetch some indicators and build simple QA charts...')
            ->addOption(
                'date',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('Use this date instead today to collect data (use format "%s")', AbstractDataSerie::DATE_FORMAT),
                (new DateTime())->format(AbstractDataSerie::DATE_FORMAT)
            )
            ->addOption('no-report', null, InputOption::VALUE_NONE, 'Do not execute the report rendering step')
            ->addOption('no-track', null, InputOption::VALUE_NONE, 'Do not execute the tracking step')
            ->addOption(
                'reset-data-series',
                null,
                InputOption::VALUE_NONE,
                'Remove all data series before collecting',
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (file_exists(Root::getConfigPath())) {
            return;
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            sprintf(
                "\nFile %s does not exists.\nDo you want to create it from the sample file ? (Y/n)",
                Root::getConfigPath()
            ),
            true
        );

        if (!$helper->ask($input, $output, $question)) {
            $io->warning(sprintf('The config file has not been created'));
        } else {
            $fs = new Filesystem();
            $fs->copy(Configuration::exampleConfigPath(), Root::getConfigPath());

            $io->success(sprintf(
                "The config file has been created at \"%s\".\nYou can now edit it to put your own configuration.",
                Root::getConfigPath()
            ));
        }

        $this->setCode(function () {
            return static::EXIT_FAILURE;
        });
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws SyntaxError
     * @throws JsonException
     * @throws LoaderError
     *
     * @throws RuntimeError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $twig = TwigFactory::getTwig();
        try {
            $io->title('Track your QA indicators');

            $io->write('Configuration');
            $io->listing(
                [
                    'Files read from : ' . Root::external(),
                    'Config read from : ' . Root::getConfigPath(),
                    'Generated files write in : ' . static::getGeneratedDir(),
                ]
            );

            /** @var ConsoleSectionOutput $section */
            $section = $output->section();
            $message = 'Initializing...';
            $section->writeln($message);
            $trackDate = $input->getOption('date');
            static::initializeTrackDate($trackDate);
            $config = Configuration::load(Root::getConfigPath());
            $section->overwrite($message . static::OUTPUT_DONE);

            /** @var ConsoleSectionOutput $section */
            $section = $output->section();
            $message = 'Loading data series...';
            $section->writeln($message);
            $dataSeriesConfig = $config['qatracker']['dataSeries'];
            $dataSeriesStack = $this->loadDataSeries($dataSeriesConfig);
            $section->overwrite($message . static::OUTPUT_DONE);
            $io->newLine();

            $withTrack = !$input->getOption('no-track');
            $withReport = !$input->getOption('no-report');
            $resetDataSeries = $input->getOption('reset-data-series');
            $graphs = [];

            $warnings = [];
            if ($withTrack) {
                try {
                    /** @var AbstractDataSerie $dataSerie */
                    foreach ($dataSeriesStack as $dataSerie) {
                        /** @var ConsoleSectionOutput $section */
                        $section = $output->section();
                        $message = sprintf('Collecting new indicator for "%s"...', $dataSerie->getId());
                        $section->writeln($message);

                        try {
                            $dataSerie->collect(static::getTrackDate(), $resetDataSeries);
                        } catch (FileNotFoundException $e) {
                            $warnings [] = $e->getMessage();
                            continue;
                        }
                        $section->overwrite($message . static::OUTPUT_DONE);
                    }
                    $io->newLine();
                } catch (\Throwable $t) {
                    $io->newLine();
                    $io->error([$t->getMessage(), 'Have you installed the tools ? See: bin/install-tools']);
                    return Command::FAILURE;
                }

                if ($warnings !== []) {
                    $io->warning($warnings);
                }
            }

            if ($withReport) {
                /** @var ConsoleSectionOutput $section */
                $section = $output->section();
                $message = 'Generating charts : ';
                $section->writeln($message);

                $charts = $config['qatracker']['charts'];
                foreach ($charts as $chart) {
                    $chart = new Chart($chart, $dataSeriesStack);

                    $graphs[] = ChartGenerator::generate(
                        $chart->getFirstProvider()->getData(),
                        $chart->getType(),
                        $chart->getGraphSettings()
                    );
                    $message .= '.';
                    $section->overwrite($message);
                }
                $io->newLine();
            }

            if (!empty($graphs)) {
                /** @var ConsoleSectionOutput $section */
                $section = $output->section();
                $message = sprintf('Rendering report...');
                $section->writeln($message);

                $html = $twig->render(static::DEFAULT_TEMPLATE, [
                    'graphs' => $graphs,
                    'js' => SVGGraph::fetchJavascript(),
                    'version' => static::VERSION,
                    'generatedAt' => new DateTime(),
                ]);
                file_put_contents(static::getReportPath(), $html);
                $section->overwrite($message . static::OUTPUT_DONE);
                $io->newLine();
            }

            $io->success(
                [
                    sprintf(
                        "Well done ! You have track new QA indicators !\n" .
                        'Report generated at : %s',
                        static::getReportPath()
                    ),
                ]
            );

            return static::EXIT_SUCCESS;
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());

            return static::EXIT_FAILURE;
        }
    }

    /**
     * @param $providersConfig
     *
     * @return array
     * @throws JsonException
     *
     */
    protected function loadDataSeries($providersConfig): array
    {
        $dataSeriesStack = [];

        /*
         * Load standard providers on a first time
         */
        foreach ($providersConfig as $key => $provider) {
            if (!AbstractDataSerie::isStandard($provider)) {
                continue;
            }

            $provider = new DataStandardSerie($provider, Root::BASE_DIR, static::getGeneratedDir());
            $dataSeriesStack[$provider->getId()] = $provider;
            unset($providersConfig[$key]);
        }

        /*
         * Load other providers on a second time
         */
        foreach ($providersConfig as $key => $provider) {
            if (!AbstractDataSerie::isPercent($provider)) {
                continue;
            }

            $provider = new DataPercentSerie($provider, static::getGeneratedDir(), $dataSeriesStack);
            $dataSeriesStack[$provider->getId()] = $provider;
            unset($providersConfig[$key]);
        }

        return $dataSeriesStack;
    }

}
