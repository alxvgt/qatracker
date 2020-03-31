<?php

namespace App\Command;

use App\Chart\ChartGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TrackCommand extends Command
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;

    protected static $defaultName = 'track';

    protected function configure()
    {
        $this
            ->setDescription('Track your QA indicators')
            ->setHelp('This command allows you to fetch some indicators and build simple QA chats...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Tack your QA indicators');

        $outputFileDir = '.qatracker/output';
        $outputFilePath = ''.$outputFileDir.'/index.html';
        $templatesDir = 'templates';
        $defautlTemplate = 'index.html.twig';

        $values = [
            ['Dough' => 30, 'Ray' => 50, 'Me' => 40, 'So' => 25, 'Far' => 45, 'Lard' => 35],
            ['Dough' => 20, 'Ray' => 30, 'Me' => 20, 'So' => 15, 'Far' => 25, 'Lard' => 35, 'Tea' => 45]
        ];

        $graph = ChartGenerator::generate($values);
        $loader = new FilesystemLoader($templatesDir);
        $twig = new Environment($loader);
        $html = $twig->render($defautlTemplate, ['graph' => $graph]);


        if (!is_dir($outputFileDir) && !mkdir($outputFileDir, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputFileDir));
        }

        file_put_contents($outputFilePath, $html);

        $io->success('Report generated at : '.$outputFilePath);

        return static::EXIT_SUCCESS;

    }


}