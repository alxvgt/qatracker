<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

class JsonPathAverageProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new JsonPathAverageProvider('tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(93.41, $result);
    }
}