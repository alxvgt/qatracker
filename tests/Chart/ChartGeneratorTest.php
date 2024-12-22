<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\Chart;

use Alxvng\QATracker\Chart\ChartGenerator;
use PHPUnit\Framework\TestCase;

class ChartGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $values = [1, 2, 3, 3, 4];
        $svgString = ChartGenerator::generate(
            $values,
            [
                'structure' => [
                    'key' => 0,
                    'value' => [1],
                ],
            ],
        );
        $this->assertIsString($svgString);
    }
}
