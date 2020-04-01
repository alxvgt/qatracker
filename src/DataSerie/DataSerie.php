<?php


namespace App\DataSerie;


use App\Command\TrackCommand;
use JsonException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class DataSerie
{
    public const DATA_SERIES_DIR = 'data-series';
    public const DATE_FORMAT = 'YmdHis';

    protected string $storageFilePath;
    protected string $name;
    protected string $slug;
    protected array $data = [];
    protected string $provider;
    protected array $arguments;

    /**
     * DataSerie constructor.
     * @param array $config
     * @throws JsonException
     */
    public function __construct(array $config)
    {
        $slugger = new AsciiSlugger();
        $this->slug = u($slugger->slug($config['name']))->lower();

        $storageDir = TrackCommand::getGeneratedDir().'/'.static::DATA_SERIES_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->name = $config['name'];
        $this->provider = $config['provider'];
        $this->arguments = $config['arguments'];

        $this->load();
    }

    /**
     * @throws JsonException
     */
    public function save()
    {
        $dir = dirname($this->getStorageFilePath());
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($this->getStorageFilePath(), json_encode($this->data, JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @throws JsonException
     */
    protected function load()
    {
        if (!file_exists($this->getStorageFilePath())) {
            return;
        }

        $this->data = json_decode(file_get_contents($this->getStorageFilePath()), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string
     */
    public function getStorageFilePath(): string
    {
        return $this->storageFilePath;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param $value
     */
    public function addData($value): void
    {
        $today = new \DateTime();
        $this->data[$today->format(static::DATE_FORMAT)] = $value;
    }

    /**
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * @return mixed|string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return array|mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

}