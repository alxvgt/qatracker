<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\JsonPathCountProvider;
use PHPUnit\Framework\TestCase;

class JsonPathCountProviderTest extends TestCase
{
    public function testFetchData(): void
    {
        $provider = new JsonPathCountProvider(getcwd(), 'tests/resource/log/phpmetrics/log.json', '$.*.mi');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(10, $result);
    }
}
