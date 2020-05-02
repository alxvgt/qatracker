<?php

namespace Alxvng\QATracker\Tests\DataProvider\Finder;

use Alxvng\QATracker\DataProvider\Finder\ProviderFinder;
use Alxvng\QATracker\Tests\Mock;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProviderFinderTest extends TestCase
{
    public function testFindById()
    {
        $serie = Mock::dataSerie();
        $serie2 = Mock::dataSerieWithBadFilePath();
        $stack = [
            $serie->getId() => $serie,
            'other-id' => $serie2,
        ];

        $result = ProviderFinder::findById($serie->getId(), $stack);
        $this->assertSame($serie, $result);
    }

    public function testFindByIdException()
    {
        $this->expectException(RuntimeException::class);

        $serie = Mock::dataSerie();
        $serie2 = Mock::dataSerieWithBadFilePath();
        $stack = [
            $serie->getId() => $serie,
            $serie2->getId() => $serie2,
        ];

        ProviderFinder::findById('bad-id', $stack);
    }
}
