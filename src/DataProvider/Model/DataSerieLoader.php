<?php

namespace Alxvng\QATracker\DataProvider\Model;

use Alxvng\QATracker\Configuration\Configuration;
use Alxvng\QATracker\Root\Root;

class DataSerieLoader
{
    public function load()
    {
        $dataSeriesStack = [];
        $providersConfig = Configuration::getDataSeries();

        /*
         * Load standard providers on a first time
         */
        foreach ($providersConfig as $key => $provider) {
            if (!AbstractDataSerie::isStandard($provider)) {
                continue;
            }

            $provider = new DataStandardSerie($provider, Root::BASE_DIR, Configuration::getGeneratedDir());
            $dataSeriesStack[$provider->getId()] = $provider;
            unset($providersConfig[$key]);
        }

        /*
         * Load other providers on a second time
         */
        foreach ($providersConfig as $key => $provider) {
            if (!AbstractDataSerie::isPercent($provider)) {
                continue;
            }

            $provider = new DataPercentSerie($provider, Configuration::getGeneratedDir(), $dataSeriesStack);
            $dataSeriesStack[$provider->getId()] = $provider;
            unset($providersConfig[$key]);
        }

        return $dataSeriesStack;

    }
}