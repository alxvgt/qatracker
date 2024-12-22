<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\Tests\Mock;
use DateTimeImmutable;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataPercentSerieTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testCollect(): void
    {
        $dataSerie = Mock::dataPercentSerie();

        $filesizeBefore = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;
        $dataBefore = $dataSerie->getData();

        $dataSerie->collect(new DateTimeImmutable(), false);

        $dataAfter = $dataSerie->getData();
        $filesizeAfter = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;

        $this->assertGreaterThan($filesizeBefore, $filesizeAfter);
        $this->assertCount(count($dataBefore) + 1, $dataAfter);
    }

    /**
     * @throws JsonException
     */
    public function testConstructorPercentException(): void
    {
        $this->expectException(RuntimeException::class);
        Mock::dataPercentSerieWithPercentProvider();
    }

    /**
     * @throws JsonException
     */
    public function testConstructorItselfException(): void
    {
        $this->expectException(RuntimeException::class);
        Mock::dataPercentSerieWithItSelfProvider();
    }
}
