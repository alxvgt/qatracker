<?php

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Chart\Chart;
use Alxvng\QATracker\Chart\ChartGenerator;
use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\DataProvider\Exception\FileNotFoundException;
use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\DataProvider\Model\DataPercentSerie;
use Alxvng\QATracker\DataProvider\Model\DataSerieLoader;
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
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsCommand(name: 'report')]
class ReportCommand extends BaseCommand
{
    private DataSerieLoader $dataSerieLoader;

    public function __construct()
    {
        parent::__construct();
        $this->dataSerieLoader = new DataSerieLoader();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate report from your QA indicators');
    }

    /**
     * @param InputInterface $input
     * @param ConsoleSectionOutput $output
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
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);
        $twig = TwigFactory::getTwig();
        try {
            $io->title('Generate report from your QA indicators');

            $fs = new Filesystem();
            $fs->mkdir(\dirname(Configuration::getReportFilename()));

            $dataSeriesStack = $this->dataSerieLoader->load();

            /** @var ConsoleSectionOutput $section */
            $section = $output->section();
            $message = 'Generating charts : ';
            $section->writeln($message);

            $graphs = [];
            foreach (Configuration::getCharts() as $chart) {
                $chart = new Chart($chart, $dataSeriesStack);

                $graphs[] = ChartGenerator::generate(
                    $chart->getChartValues(),
                    $chart->getChartStructure(),
                    $chart->getType(),
                    $chart->getGraphSettings()
                );
                $message .= '.';
                $output->writeln($message);
            }
            $io->newLine();

            if ($graphs !== []) {
                /** @var ConsoleSectionOutput $section */
                $section = $output->section();
                $message = sprintf('Rendering report...');
                $section->writeln($message);

                $html = $twig->render('index.html.twig', [
                    'graphs' => $graphs,
                    'js' => SVGGraph::fetchJavascript(),
                    'version' => Configuration::version(),
                    'generatedAt' => new DateTime(),
                ]);
                file_put_contents(Configuration::getReportFilename(), $html);
                $section->overwrite($message . ' ' . static::OUTPUT_DONE);
                $io->newLine();
            }

            $io->success(
                [
                    sprintf(
                        "Well done !\n" .
                        'Report generated at : %s',
                        Configuration::getReportFilename()
                    ),
                ]
            );

            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }

}
