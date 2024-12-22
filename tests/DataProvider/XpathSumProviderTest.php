<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\XpathSumProvider;
use PHPUnit\Framework\TestCase;

class XpathSumProviderTest extends TestCase
{
    public function testFetchData(): void
    {
        $provider = new XpathSumProvider(getcwd(), 'tests/resource/log/phploc/log.xml', '/phploc/*[starts-with(name(), \'lloc\')]');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(226, $result);
    }
}
