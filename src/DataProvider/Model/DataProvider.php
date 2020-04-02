<?php


namespace App\DataProvider\Model;


use App\Command\TrackCommand;
use JsonException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class DataProvider
{
    public const PROVIDERS_DIR = 'providers-data';
    public const DATE_FORMAT = 'YmdHis';

    protected string $storageFilePath;
    protected string $id;
    protected string $slug;
    protected array $data = [];
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
    public function getId() : string
    {
        return $this->id;
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

    /**
     * @throws JsonException
     */
    public function collect(): void
    {
        $providerClass = $this->getClass();
        $provider = new $providerClass(...$this->getArguments());
        $value = $provider->fetchData();
        $this->addData($value);
        $this->save();
    }

}