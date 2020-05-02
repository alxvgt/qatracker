<?php

namespace Alxvng\QATracker\Tests\Command;

use Alxvng\QATracker\Command\TrackCommand;
use Alxvng\QATracker\Configuration\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class TrackCommandTest extends TestCase
{
    public function testExecuteNoConfigFile()
    {
        $fs = new Filesystem();
        $fs->remove(TrackCommand::getBaseDir());

        $commandTester = $this->getCommandTester();

        $this->assertStringContainsString('config file has been created', $commandTester->getDisplay());
        $this->assertStringContainsString('.qatracker/config.yaml', $commandTester->getDisplay());
        $this->assertStringContainsString('.qatracker/config.yaml does not exists.', $commandTester->getErrorOutput());
        $this->assertStringContainsString('create it from the sample file ? (Y/n)', $commandTester->getErrorOutput());
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithConfigFile()
    {
        $fs = new Filesystem();
        $fs->copy(
            Configuration::exampleConfigPath(),
            TrackCommand::getConfigPath()
        );

        $commandTester = $this->getCommandTester();

        $this->assertStringContainsString('Collecting new indicator for "lines-of-code"', $commandTester->getDisplay());
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    protected function getCommandTester(): CommandTester
    {
        $application = new Application();
        $command = new TrackCommand();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [],
            [
                'interactive' => true,
                'capture_stderr_separately' => true,
            ]
        );

        return $commandTester;
    }
}
