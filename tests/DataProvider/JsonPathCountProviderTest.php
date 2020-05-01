<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\JsonPathCountProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
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
