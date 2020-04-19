<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

class XpathAverageProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new XpathAverageProvider('tests/resource/log/phploc/log.xml', '/phploc/*[starts-with(name(), \'lloc\')]');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(45.2, $result);
    }
}