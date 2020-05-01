<?php


namespace Alxvng\QATracker\Tests;


use Alxvng\QATracker\DataProvider\Model\DataPercentSerie;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use JsonException;

class Mock
{

    /**
     * @return DataStandardSerie
     * @throws JsonException
     */
    public static function dataSerieWithBadXml(?string $id = null): DataStandardSerie
    {
        $config = [
            'id' => $id ?? 'lines-of-code',
            'class' => 'Alxvng\QATracker\DataProvider\XpathProvider',
            'arguments' => [
                'tests/resource/log/phploc/badxml.xml',
                '/phploc/loc',
            ]
        ];

        return new DataStandardSerie($config, '/tmp');
    }

    /**
     * @param string|null $id
     * @return DataStandardSerie
     * @throws JsonException
     */
    public static function dataSerie(?string $id = null): DataStandardSerie
    {
        $config = [
            'id' => $id ?? 'lines-of-code',
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
    public static function dataSerieWithBadFilePath(?string $id = null): DataStandardSerie
    {
        $config = [
            'id' => $id ?? 'lines-of-code',
            'class' => 'Alxvng\QATracker\DataProvider\XpathProvider',
            'arguments' => [
                'bad-path.xml',
                '/phploc/loc',
            ]
        ];

        return new DataStandardSerie($config, '/tmp');
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    public static function dataPercentSerieWithPercentProvider(): DataPercentSerie
    {
        $config = [
            'id' => 'total-duplicated-lines-percent',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        $percent = new DataPercentSerie($config,
            '/tmp',
            [
                Mock::dataSerie()->getId() => Mock::dataSerie(),
            ]);

        $config = [
            'id' => 'total-duplicated-lines-percent-2',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'total-duplicated-lines-percent',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                Mock::dataSerie()->getId() => Mock::dataSerie(),
                $percent->getId() => $percent,
            ]);
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    public static function dataPercentSerie(): DataPercentSerie
    {
        $config = [
            'id' => 'total-duplicated-lines-percent',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                Mock::dataSerie()->getId() => Mock::dataSerie(),
                Mock::dataSerie()->getId() => Mock::dataSerie(),
            ]);
    }

    /**
     * @return DataPercentSerie
     * @throws JsonException
     */
    public static function dataPercentSerieWithItSelfProvider(): DataPercentSerie
    {
        $config = [
            'id' => 'lines-of-code',
            'provider' => 'lines-of-code',
            'totalPercentProvider' => 'lines-of-code',
        ];

        return new DataPercentSerie($config,
            '/tmp',
            [
                Mock::dataSerie()->getId() => Mock::dataSerie(),
            ]);

    }
}