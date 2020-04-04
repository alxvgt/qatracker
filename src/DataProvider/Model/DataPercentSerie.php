<?php


namespace App\DataProvider\Model;


use App\Command\TrackCommand;
use App\DataProvider\Finder\ProviderFinder;
use JsonException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class DataPercentSerie extends AbstractDataSerie
{
    protected AbstractDataSerie $provider;
    protected AbstractDataSerie $totalPercentProvider;

    /**
     * DataProvider constructor.
     * @param array $config
     * @throws JsonException
     */
    public function __construct(array $config, array $providersStack)
    {
        $slugger = new AsciiSlugger();
        $this->slug = u($slugger->slug($config['id']))->lower();

        $storageDir = TrackCommand::getGeneratedDir().'/'.static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->id = $config['id'];

        $this->provider = ProviderFinder::findById($config['provider'], $providersStack);
        $this->totalPercentProvider = ProviderFinder::findById($config['totalPercentProvider'], $providersStack);

        $this->load();
    }


    /**
     * @throws JsonException
     */
    public function collect(): void
    {
        $provider = $this->getProvider();
        if (!$provider instanceof DataStandardSerie) {
            throw new \RuntimeException(sprintf('Unable to collect data. %s expect provider, got %s',
                DataStandardSerie::class, get_class($provider)));
        }

        $totalPercentProvider = $this->getTotalPercentProvider();
        if (!$totalPercentProvider instanceof DataStandardSerie) {
            throw new \RuntimeException(sprintf('Unable to collect data. %s expect provider, got %s',
                DataStandardSerie::class, get_class($totalPercentProvider)));
        }

        $value = $provider->getInstance()->fetchData();
        $total = $totalPercentProvider->getInstance()->fetchData();
        $percent = $value * 100 / $total;

        $this->addData($percent);
        $this->save();
    }

    /**
     * @return AbstractDataSerie
     */
    public function getProvider(): AbstractDataSerie
    {
        return $this->provider;
    }

    /**
     * @return AbstractDataSerie
     */
    public function getTotalPercentProvider(): AbstractDataSerie
    {
        return $this->totalPercentProvider;
    }


}