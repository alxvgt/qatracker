<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

class JsonPathSumProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new JsonPathSumProvider('tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(934.07, $result);
    }
}