<?php

namespace Alxvng\QATracker\Chart;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Goat1000\SVGGraph\LineGraph;
use Goat1000\SVGGraph\SVGGraph;

class ChartGenerator
{
    /**
     * @param        $values
     * @param string $type
     * @param array $settings
     *
     * @return mixed
     */
    public static function generate($values, $structure, $type = LineGraph::class, array $settings = [])
    {
        $dark = 'rgb(54,54,54)';
        $darkRed = 'rgb(127,0,0)';
        $font = 'BlinkMacSystemFont,-apple-system,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",Helvetica,Arial,sans-serif';

        $settings = array_merge(
            $structure,
            [
                'auto_fit' => true,
                'back_colour' => 'none',
                'pad_left' => 0,
                'pad_right' => 0,
                'pad_top' => 2,
                'pad_bottom' => 2,
                'back_stroke_width' => 0,
                'graph_title_colour' => $dark,
                'graph_title_font' => $font,
                'graph_title_font_size' => 18,
                'axis_font_size' => 12,
                'tooltip_font_size' => 14,
                'axis_colour' => $dark,
                'axis_text_colour' => $dark,
                'axis_text_space_v' => 5,
                'axis_font' => $font,
                'axis_stroke_width' => 1,
                'axis_text_space' => 10,
                'label_font' => $font,
                'label_font_size' => 15,
                'data_label_fade_in_speed' => 40,
                'data_label_fade_out_speed' => 5,
                'data_label_click' => 'hide',
                'data_label_shadow_opacity' => 0,
                'data_label_padding' => 1,
//                'marker_colour' => $darkRed,
//                'marker_stroke_colour' => $darkRed,
                'line_stroke_width' => 2,
                'datetime_keys' => true,
                'datetime_key_format' => AbstractDataSerie::DATE_FORMAT,
                'datetime_text_format' => 'j M',
                'minimum_grid_spacing_h' => 40,
                'show_grid_subdivisions' => true,
                'thousands' => ' ',
                'precision' => 2,
                'exception_throw' => false,
            ],
            $settings
        );

        $width = 550;
        $height = 300;

        $graph = new SVGGraph($width, $height, $settings);
        $graph->values($values);

        return $graph->fetch($type);
    }
}
