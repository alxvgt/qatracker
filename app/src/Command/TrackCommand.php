<?php

namespace App\Command;

use App\Chart\ChartGenerator;
use App\DataProvider\XpathProvider;
use App\DataSerie\DataSerie;
use App\Twig\TwigFactory;
use Goat1000\SVGGraph\LineGraph;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrackCommand extends Command
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;
    public const BASE_DIR = '.qatracker';

    protected static $defaultName = 'track';

    protected function configure()
    {
        $this
            ->setDescription('Track your QA indicators')
            ->setHelp('This command allows you to fetch some indicators and build simple QA chats...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $twig = TwigFactory::getTwig();

        $io->title('Track your QA indicators');

        $outputFileDir = static::BASE_DIR.'/output';
        $outputFilePath = ''.$outputFileDir.'/index.html';
        $defautlTemplate = 'index.html.twig';

        if (!is_dir($outputFileDir) && !mkdir($outputFileDir, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputFileDir));
        }

        $io->text('Collecting new indicator value...');
        $provider = new XpathProvider('qa/phploc/log.xml', '/phploc/loc');
        $value = $provider->fetchData();
        $io->text('done.');
        $io->newLine();

        $io->text('Updating data serie...');
        $dataSerie = new DataSerie('loc');
        $dataSerie->addData($value);
        $dataSerie->save();
        $io->text('done.');
        $io->newLine();

        $io->text('Rendering report...');
        $graph = ChartGenerator::generate($dataSerie->getData());
        $html = $twig->render($defautlTemplate, [
            'graphs' => [
                [
                    'graph' => $graph->fetch(LineGraph::class),
                    'js'    => $graph::fetchJavascript(),
                ],
            ],
        ]);
        file_put_contents($outputFilePath, $html);
        $io->text('done.');
        $io->newLine();



        $io->success('Report generated at : '.$outputFilePath);

        return static::EXIT_SUCCESS;

    }


}