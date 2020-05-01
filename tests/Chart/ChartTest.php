<?php

namespace Alxvng\QATracker\Tests\Chart;

use Alxvng\QATracker\Chart\Chart;
use Alxvng\QATracker\Tests\Mock;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 * @coversNothing
 */
class ChartTest extends TestCase
{
    public function testConstructException()
    {
        $this->expectException(RuntimeException::class);

        $config['type'] = 'test';
        $config['withHistory'] = 'test';
        $config['graphSettings'] = [];
        $config['dataSeries'] = [];
        $providersStack = [];

        new Chart($config, $providersStack);
    }

    public function testConstruct()
    {
        $config['type'] = 'test';
        $config['withHistory'] = 'test';
        $config['graphSettings'] = [];
        $config['dataSeries'] = [
            'test1' => '01',
            'test2' => '02',
            'test3' => '03',
        ];
        $providersStack = [
            '01' => 'test1',
            '02' => 'test2',
            '04' => 'test4',
        ];

        $chart = new Chart($config, $providersStack);
        $this->assertCount(2, $chart->getProviders());
        $this->assertArrayHasKey('02', $chart->getProviders());
    }

    /**
     * @throws JsonException
     */
    public function testGetFirstProvider()
    {
        $config['type'] = 'test';
        $config['withHistory'] = 'test';
        $config['graphSettings'] = [];
        $config['dataSeries'] = [
            'test1' => '01',
            'test2' => '02',
            'test3' => '03',
        ];
        $providersStack = [
            '01' => Mock::dataSerie('S-001'),
            '02' => Mock::dataSerie('S-002'),
            '04' => 'test4',
        ];

        $chart = new Chart($config, $providersStack);
        $this->assertEquals('S-001', $chart->getFirstProvider()->getId());
    }
}
