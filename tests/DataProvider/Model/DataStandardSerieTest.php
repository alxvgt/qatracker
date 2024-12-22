<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use Alxvng\QATracker\DataProvider\Exception\FileNotFoundException;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use Alxvng\QATracker\Tests\Mock;
use DateTimeImmutable;
use Generator;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataStandardSerieTest extends TestCase
{
    /**
     * @dataProvider getInstanceProvider
     */
    public function testGetInstance(DataStandardSerie $object, ?callable $assert = null, ?callable $expectException = null): void
    {
        if ($expectException) {
            $expectException($this);
        }

        $result = $object->getInstance();

        if ($assert) {
            $assert($result);
        }
    }

    /**
     * @return Generator
     *
     * @throws JsonException
     */
    public function getInstanceProvider()
    {
        yield [Mock::dataSerie(), function ($result): void {
            $this->assertInstanceOf(DataProviderInterface::class, $result);
        }];

        yield [Mock::dataSerieWithBadFilePath(), null, function (TestCase $self): void {
            $self->expectException(FileNotFoundException::class);
        }];

        yield [Mock::dataSerieWithBadXml(), null, function (TestCase $self): void {
            $self->expectException(RuntimeException::class);
        }];
    }

    /**
     * @throws JsonException
     */
    public function testCollect(): void
    {
        $dataSerie = Mock::dataSerie();

        $filesizeBefore = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;
        $dataBefore = $dataSerie->getData();

        $dataSerie->collect(new DateTimeImmutable(), false);

        $dataAfter = $dataSerie->getData();
        $filesizeAfter = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;

        $this->assertGreaterThan($filesizeBefore, $filesizeAfter);
        $this->assertCount(count($dataBefore) + 1, $dataAfter);
    }
}
