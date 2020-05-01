<?php

namespace Alxvng\QATracker\Tests\Chart;

use Alxvng\QATracker\Chart\ChartGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ChartGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $values = [1, 2, 3, 3, 4];
        $svgString = ChartGenerator::generate($values);
        $this->assertIsString($svgString);
    }
}
