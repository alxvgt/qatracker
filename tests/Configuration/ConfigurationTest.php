<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\Configuration;

use Alxvng\QATracker\Configuration\Configuration;
use Generator;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConfigurationTest extends TestCase
{
    public function testExampleConfigPath(): void
    {
        $this->assertIsString(Configuration::exampleConfigPath());
        $this->assertStringContainsString('.qatracker.dist/config.yaml', Configuration::exampleConfigPath());
    }

    /**
     * @dataProvider loadWithExceptionProvider
     */
    public function testLoadWithException(string $configPath, string $exceptionClass): void
    {
        $this->expectException($exceptionClass);
        Configuration::load($configPath);
    }

    /**
     * @dataProvider loadWithProvider
     */
    public function testLoad(string $configPath): void
    {
        $config = Configuration::load($configPath);
        $this->assertIsArray($config);
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
