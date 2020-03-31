<?php

namespace App\Chart;

use Goat1000\SVGGraph\SVGGraph;

class ChartGenerator
{
    public static function generate($values)
    {
        $settings = [
            'back_colour' => '#fff',
            'axis_font' => 'Arial',
        ];

        $width = 500;
        $height = 300;
        $type = 'LineGraph';

        $colours = [ [ 'red', 'yellow' ], [ 'blue', 'white' ] ];
        $graph = new SVGGraph($width, $height, $settings);
        $graph->colours($colours);

        $graph->values($values);
        return $graph->fetch($type);
    }
}