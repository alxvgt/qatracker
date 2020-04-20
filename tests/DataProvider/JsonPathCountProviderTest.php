<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

class JsonPathCountProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new JsonPathCountProvider('tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(10, $result);
    }
}