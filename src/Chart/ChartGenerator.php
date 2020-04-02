<?php

namespace App\Chart;

use App\DataProvider\Model\DataProvider;
use Goat1000\SVGGraph\LineGraph;
use Goat1000\SVGGraph\SVGGraph;

class ChartGenerator
{
    /**
     * @param        $values
     * @param string $type
     * @param array  $settings
     * @return mixed
     */
    public static function generate($values, $type = LineGraph::class, $settings = [])
    {
        $settings = array_merge([
            'auto_fit'              => true,
            'graph_title_font_size' => 16,
            'datetime_keys'         => true,
            'datetime_key_format'   => DataProvider::DATE_FORMAT,
            'datetime_text_format'  => "j M\nY",
            'back_colour'           => '#fff',
            'axis_font'             => 'Arial',
            'minimum_grid_spacing_h' => 40,
            'show_grid_subdivisions' => true,
        ], $settings);

        $width = 550;
        $height = 300;

        $graph = new SVGGraph($width, $height, $settings);
        $graph->values($values);

        return $graph->fetch($type);
    }
}