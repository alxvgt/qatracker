<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\XpathProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class XpathProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new XpathProvider('tests/resource/log/phploc/log.xml', '/phploc/loc');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(441, $result);
    }
}
