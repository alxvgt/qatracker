<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\XpathProvider;
use PHPUnit\Framework\TestCase;

class XpathProviderTest extends TestCase
{
    public function testFetchData(): void
    {
        $provider = new XpathProvider(getcwd(), 'tests/resource/log/phploc/log.xml', '/phploc/loc');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(441, $result);
    }
}
