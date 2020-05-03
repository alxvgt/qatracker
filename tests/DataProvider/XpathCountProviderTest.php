<?php

namespace Alxvng\QATracker\Tests\DataProvider;

use Alxvng\QATracker\DataProvider\XpathCountProvider;
use PHPUnit\Framework\TestCase;

class XpathCountProviderTest extends TestCase
{
    public function testFetchData()
    {
        $provider = new XpathCountProvider(getcwd(), 'tests/resource/log/phploc/log.xml', '/phploc/*[starts-with(name(), \'lloc\')]');
        $result = $provider->fetchData();
        $this->assertIsFloat($result);
        $this->assertEquals(5, $result);
    }
}
