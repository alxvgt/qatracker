<?php

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\Tests\Mock;
use DateTime;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataPercentSerieTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testCollect()
    {
        $dataSerie = Mock::dataPercentSerie();

        $filesizeBefore = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;
        $dataBefore = $dataSerie->getData();

        $dataSerie->collect(new DateTime());

        $dataAfter = $dataSerie->getData();
        $filesizeAfter = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;

        $this->assertGreaterThan($filesizeBefore, $filesizeAfter);
        $this->assertCount(count($dataBefore) + 1, $dataAfter);
    }

    /**
     * @throws JsonException
     */
    public function testConstructorPercentException()
    {
        $this->expectException(RuntimeException::class);
        Mock::dataPercentSerieWithPercentProvider();
    }

    /**
     * @throws JsonException
     */
    public function testConstructorItselfException()
    {
        $this->expectException(RuntimeException::class);
        Mock::dataPercentSerieWithItSelfProvider();
    }
}
