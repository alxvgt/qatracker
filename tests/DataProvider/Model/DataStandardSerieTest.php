<?php

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use DateTime;
use Error;
use Generator;
use JsonException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataStandardSerieTest extends TestCase
{
    /**
     * @param DataStandardSerie $object
     * @param callable $assert
     * @param callable $expectException
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
     * @return Generator
     * @throws JsonException
     */
    public function getInstanceProvider()
    {
        yield [static::getDataSerie(), function ($result) {
            $this->assertInstanceOf(DataProviderInterface::class, $result);
        }];

        yield [static::getDataSerieWithBadFilePath(), null, function (TestCase $self) {
            $self->expectException(RuntimeException::class);
        }];

        yield [static::getDataSerieWithBadXml(), null, function (TestCase $self) {
            $self->expectException(RuntimeException::class);
        }];
    }

    /**
     * @throws JsonException
     */
    public function testCollect()
    {
        $dataSerie = $this->getDataSerie();

        $filesizeBefore = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;
        $dataBefore = $dataSerie->getData();

        $dataSerie->collect(new DateTime());

        $dataAfter = $dataSerie->getData();
        $filesizeAfter = file_exists($dataSerie->getStorageFilePath()) ? filesize($dataSerie->getStorageFilePath()) : 0;

        $this->assertGreaterThan($filesizeBefore, $filesizeAfter);
        $this->assertCount(count($dataBefore) + 1, $dataAfter);
    }

    /**
     * @return DataStandardSerie
     * @throws JsonException
     */
    public static function getDataSerie(): DataStandardSerie
    {
        $config = [
            'id' => 'lines-of-code',
            'class' => 'Alxvng\QATracker\DataProvider\XpathProvider',
            'arguments' => [
                'tests/resource/log/phploc/log.xml',
                '/phploc/loc',
            ]
        ];

        return new DataStandardSerie($config, '/tmp');
    }

    /**
     * @return DataStandardSerie
     * @throws JsonException
     */
    public static function getDataSerieWithBadFilePath(): DataStandardSerie
    {
        $config = [
            'id' => 'lines-of-code',
            'class' => 'Alxvng\QATracker\DataProvider\XpathProvider',
            'arguments' => [
                'bad-path.xml',
                '/phploc/loc',
            ]
        ];

        return new DataStandardSerie($config, '/tmp');
    }

    /**
     * @return DataStandardSerie
     * @throws JsonException
     */
    public static function getDataSerieWithBadXml(): DataStandardSerie
    {
        $config = [
            'id' => 'lines-of-code',
            'class' => 'Alxvng\QATracker\DataProvider\XpathProvider',
            'arguments' => [
                'tests/resource/log/phploc/badxml.xml',
                '/phploc/loc',
            ]
        ];

        return new DataStandardSerie($config, '/tmp');
    }
}
