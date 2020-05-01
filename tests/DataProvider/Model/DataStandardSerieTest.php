<?php

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use Alxvng\QATracker\Tests\Mock;
use DateTime;
use Generator;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 * @coversNothing
 */
class DataStandardSerieTest extends TestCase
{
    /**
     * @param callable          $assert
     * @param callable          $expectException
     * @param DataStandardSerie $object
     * @dataProvider getInstanceProvider
     */
    public function testGetInstance(DataStandardSerie $object, callable $assert = null, callable $expectException = null)
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
     * @throws JsonException
     *
     * @return Generator
     */
    public function getInstanceProvider()
    {
        yield [Mock::dataSerie(), function ($result) {
            $this->assertInstanceOf(DataProviderInterface::class, $result);
        }];

        yield [Mock::dataSerieWithBadFilePath(), null, function (TestCase $self) {
            $self->expectException(RuntimeException::class);
        }];

        yield [Mock::dataSerieWithBadXml(), null, function (TestCase $self) {
            $self->expectException(RuntimeException::class);
        }];
    }

    /**
     * @throws JsonException
     */
    public function testCollect()
    {
        $dataSerie = Mock::dataSerie();

        $filesizeBefore = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;
        $dataBefore = $dataSerie->getData();

        $dataSerie->collect(new DateTime());

        $dataAfter = $dataSerie->getData();
        $filesizeAfter = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;

        $this->assertGreaterThan($filesizeBefore, $filesizeAfter);
        $this->assertCount(count($dataBefore) + 1, $dataAfter);
    }
}
