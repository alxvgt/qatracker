<?php


namespace Alxvng\QATracker\DataProvider;

use PHPUnit\Framework\TestCase;

class XpathCountProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new XpathCountProvider('tests/resource/log/phploc/log.xml', '/phploc/loc');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(1, $result);
    }
}