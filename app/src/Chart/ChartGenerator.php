<?php

namespace App\Chart;

use App\DataSerie\DataSerie;
use Goat1000\SVGGraph\SVGGraph;

class ChartGenerator
{
    /**
     * @param       $values
     * @param array $settings
     * @return SVGGraph
     */
    public static function generate($values, $settings = []): SVGGraph
    {
        $settings = array_merge([
            'datetime_keys'       => true,
            'datetime_key_format' => DataSerie::DATE_FORMAT,
            'datetime_text_format' => 'j M',
            'back_colour'         => '#fff',
            'axis_font'           => 'Arial',
        ], $settings);

        $width = 500;
        $height = 300;

        $graph = new SVGGraph($width, $height, $settings);
        $graph->values($values);

        return $graph;
    }
}