<?php

namespace Alxvng\QATracker\Tests\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use Alxvng\QATracker\DataProvider\Model\DataPercentSerie;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use DateTime;
use Error;
use Generator;
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
     * @param DataPercentSerie $dataSerie
     * @throws JsonException
     */
    public function testConstructorPercentException()
    {
        $this->expectException(RuntimeException::class);
        static::getDataSerieWithPercentProvider();
    }

    /**
     * @param DataPercentSerie $dataSerie
     * @throws JsonException
     */
    public function testConstructorItselfException()
    {
        $this->expectException(RuntimeException::class);
        static::getDataSerieWithItSelfProvider();
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    protected function getDataSerie(): DataPercentSerie
    {
        $config = [
            'id' => 'total-duplicated-lines-percent',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                DataStandardSerieTest::getDataSerie()->getId() => DataStandardSerieTest::getDataSerie(),
                DataStandardSerieTest::getDataSerie()->getId() => DataStandardSerieTest::getDataSerie(),
            ]);
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    protected static function getDataSerieWithPercentProvider(): DataPercentSerie
    {
        $config = [
            'id' => 'total-duplicated-lines-percent',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        $percent = new DataPercentSerie($config,
            '/tmp',
            [
                DataStandardSerieTest::getDataSerie()->getId() => DataStandardSerieTest::getDataSerie(),
            ]);

        $config = [
            'id' => 'total-duplicated-lines-percent-2',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'total-duplicated-lines-percent',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                DataStandardSerieTest::getDataSerie()->getId() => DataStandardSerieTest::getDataSerie(),
                $percent->getId() => $percent,
            ]);
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    protected static function getDataSerieWithItSelfProvider(): DataPercentSerie
    {
        $config = [
            'id' => 'lines-of-code',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                DataStandardSerieTest::getDataSerie()->getId() => DataStandardSerieTest::getDataSerie(),
            ]);

    }
}
