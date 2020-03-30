<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

        $io->title("Tack your QA indicators");

        return static::EXIT_SUCCESS;
    }


}