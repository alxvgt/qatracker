<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\JsonPathSumProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
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
