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
use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
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
class TrackCommand extends BaseCommand
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
            ->setDescription('Track your QA indicators')
            ->addOption(
                'date',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('Use this date instead today to collect data (use format "%s")', AbstractDataSerie::DATE_FORMAT),
                (new DateTime())->format(AbstractDataSerie::DATE_FORMAT)
            )
            ->addOption(
                'reset-data-series',
                null,
                InputOption::VALUE_NONE,
                'Remove all data series before collecting',
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $io = new SymfonyStyle($input, $output);
        try {
            $io->title('Track your QA indicators');

            $trackDate = new DateTimeImmutable($input->getOption('date'));
            $resetDataSeries = $input->getOption('reset-data-series');
            $dataSeriesStack = $this->dataSerieLoader->load();

            try {
                /** @var AbstractDataSerie $dataSerie */
                foreach ($dataSeriesStack as $dataSerie) {

                    /** @var ConsoleSectionOutput $section */
                    $section = $output->section();
                    $message = sprintf('Collecting new indicator for "%s"...', $dataSerie->getId());
                    $section->writeln($message);

                    try {
                        $dataSerie->collect($trackDate, $resetDataSeries);
                    } catch (FileNotFoundException $t) {
                        $section->overwrite($message . ' ' . static::OUTPUT_SKIP . self::yellow(' (' . $t->getMessage() . ')'));
                        continue;
                    }

                    $section->overwrite($message . ' ' . static::OUTPUT_DONE);
                }
                $io->newLine();
            } catch (FileNotFoundException $t) {
                $io->warning([$t->getMessage(), 'Skip']);
            } catch (\Throwable $t) {
                $io->newLine();
                $io->error([$t->getMessage(), 'Have you installed the tools ? See help.']);
                return Command::FAILURE;
            }

            $io->success("Well done ! You have track new QA indicators !");

            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

}
