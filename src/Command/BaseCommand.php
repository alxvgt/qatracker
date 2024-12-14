<?php

namespace Alxvng\QATracker\Command;

use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\Root\Root;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;

abstract class BaseCommand extends Command
{
    protected const OUTPUT_DONE = '<fg=green>done</>.';
    protected const OUTPUT_SKIP = '<fg=yellow>skip</>.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dotenv = new Dotenv();
        $dotenv->populate([
            'QT_CONFIG_DIR' => Root::getConfigDir(),
            'QT_TMP_DIR' => Configuration::tmpDir(),
            'QT_PROJECT_DIR' => Configuration::projectTmpDir(),
        ]);

        return Command::SUCCESS;
    }

    protected static function yellow(string $string): string
    {
        return \sprintf('<fg=yellow>%s</>', $string);
    }
}
