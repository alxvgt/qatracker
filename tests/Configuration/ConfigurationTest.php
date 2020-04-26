<?php

namespace Alxvng\QATracker\Tests\Configuration;

use Alxvng\QATracker\Configuration\Configuration;
use Generator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConfigurationTest extends TestCase
{
    public function testExampleConfigPath()
    {
        $this->assertIsString(Configuration::exampleConfigPath());
        $this->assertStringContainsString('.qatracker.dist/config.yaml', Configuration::exampleConfigPath());
    }

    /**
     * @param string $configPath
     * @param string $exceptionClass
     * @dataProvider loadWithExceptionProvider
     */
    public function testLoadWithException(string $configPath, string $exceptionClass)
    {
        $this->expectException($exceptionClass);
        Configuration::load($configPath);
    }

    /**
     * @param string $configPath
     * @dataProvider loadWithProvider
     */
    public function testLoad(string $configPath)
    {
        $config = Configuration::load($configPath);
        $this->assertIsArray($config);

        $this->assertCount(10, $config['qatracker']['dataSeries']);
        $this->assertEquals('non-comment-lines-of-code', $config['qatracker']['dataSeries']['non-comment-lines-of-code']['id']);
        $this->assertEquals('/phploc/ncloc', $config['qatracker']['dataSeries']['non-comment-lines-of-code']['arguments'][1]);

        $this->assertCount(3, $config['qatracker']['charts']);
        $this->assertEquals('line-of-code-bar', $config['qatracker']['charts']['line-of-code-bar']['id']);
        $this->assertEquals('Lines of code', $config['qatracker']['charts']['line-of-code-bar']['graphSettings']['graph_title']);
    }

    /**
     * @return Generator
     */
    public function loadWithExceptionProvider()
    {
        yield ['tests/resource/config/not-exist.yaml', RuntimeException::class];
        yield ['tests/resource/config/empty.yaml', RuntimeException::class];
        yield ['tests/resource/config/onlyRootKey.yaml', RuntimeException::class];
        yield ['tests/resource/config/withoutCharts.yaml', RuntimeException::class];
        yield ['tests/resource/config/withoutData.yaml', RuntimeException::class];
        yield ['tests/resource/config/badStandardSerieClass.yaml', RuntimeException::class];
        yield ['tests/resource/config/badStandardSerieArguments.yaml', RuntimeException::class];
    }

    /**
     * @return Generator
     */
    public function loadWithProvider()
    {
        yield ['.qatracker.dist/config.yaml'];
    }

}
