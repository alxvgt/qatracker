<?php

namespace App\Tests\DataSerie;

use App\DataProvider\Model\DataStandardSerie;
use JsonException;
use PHPUnit\Framework\TestCase;
use TypeError;

class DataSerieTest extends TestCase
{

    /**
     * @throws JsonException
     */
    public function testGetSlug()
    {
        $this->expectException(TypeError::class);

        $dataSerie = new DataStandardSerie([
            'name' => 'serie test',
        ]);
    }
}
