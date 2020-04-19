<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

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