<?php


namespace App\DataProvider\Model;


use App\Command\TrackCommand;
use App\DataProvider\DataProviderInterface;
use JsonException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class DataStandardSerie extends AbstractDataSerie
{
    protected string $class;
    protected array $arguments;

    /**
     * DataProvider constructor.
     * @param array $config
     * @throws JsonException
     */
    public function __construct(array $config)
    {
        $slugger = new AsciiSlugger();
        $this->slug = u($slugger->slug($config['id']))->lower();

        $storageDir = TrackCommand::getGeneratedDir().'/'.static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->id = $config['id'];
        $this->class = $config['class'];
        $this->arguments = $config['arguments'];

        $this->load();
    }

    /**
     * @throws JsonException
     */
    public function collect(): void
    {
        $provider = $this->getInstance();
        $value = $provider->fetchData();
        $this->addData($value);
        $this->save();
    }

    /**
     * @return DataProviderInterface
     */
    public function getInstance() : DataProviderInterface
    {
        $providerClass = $this->getClass();
        return new $providerClass(...$this->getArguments());
    }

    /**
     * @return mixed|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array|mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }



}