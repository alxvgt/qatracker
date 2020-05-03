<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\JsonPathProvider;
use PHPUnit\Framework\TestCase;

class JsonPathProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new JsonPathProvider(getcwd(), 'tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(102.42, $result);
    }
}
