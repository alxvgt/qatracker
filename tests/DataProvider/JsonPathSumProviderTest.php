<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\JsonPathSumProvider;
use PHPUnit\Framework\TestCase;

class JsonPathSumProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new JsonPathSumProvider(getcwd(), 'tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(934.07, $result);
    }
}
