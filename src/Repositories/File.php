<?php

namespace MenaraSolutions\Geographer\Repositories;

use MenaraSolutions\Geographer\Contracts\IdentifiableInterface;
use MenaraSolutions\Geographer\Contracts\RepositoryInterface;
use MenaraSolutions\Geographer\Earth;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\Exceptions\FileNotFoundException;
use MenaraSolutions\Geographer\Exceptions\MisconfigurationException;
use MenaraSolutions\Geographer\Helpers\WhereAmI;
use MenaraSolutions\Geographer\State;
use MenaraSolutions\Geographer\Exceptions\ObjectNotFoundException;
use MenaraSolutions\Geographer\City;

class File implements RepositoryInterface
{
    /**
     * Path to resource files
     *
     * @var string
     */
    protected string $prefix;

    /**
     * Path to translation files
     *
     * @var string
     */
    protected string $translationsPrefix;

    /**
     * @var array $paths
     */
    protected static array $paths = [
        Earth::class => 'countries.json',
        Country::class => 'states/code.json',
        State::class => 'cities/parentCode.json'
    ];

    /**
     * @var array
     */
    protected static array $indexes = [
        Country::class => 'indexCountry.json',
        State::class => 'indexState.json'
    ];

    /**
     * @var array
     */
    protected array $cache = [];


    /**
     * File constructor.
     *
     * @param string|null $prefix
     * @param string|null $translationsPrefix
     *
     * @throws FileNotFoundException
     */
    public function __construct( string $prefix = null, string $translationsPrefix = null)
    {
        $this->prefix = $prefix ?: $this->getDefaultPath();
        $this->translationsPrefix = $translationsPrefix ?: $this->guessTranslationsPrefix();
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    private function getDefaultPath(): string
    {
        if (! class_exists(WhereAmI::class)) {
            throw new FileNotFoundException('Unable to locate data package');
        }

        return WhereAmI::path();
    }

    /**
     * @param string $class
     * @param string $prefix
     * @param array  $params
     *
     * @return string
     * @throws MisconfigurationException
     */
    public function getPath( string $class, string $prefix, array $params): string
    {
        if (! isset(self::$paths[$class])) {
            throw new MisconfigurationException($class . ' is not supposed to load data');
        }

        return $prefix . DIRECTORY_SEPARATOR . str_replace(array_keys($params), array_values($params), self::$paths[$class]);
    }

    /**
     * @return string
     */
    public function getTranslationsPrefix(): string
    {
        return $this->translationsPrefix;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setTranslationsPrefix( string $prefix): self
    {
        $this->translationsPrefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function guessTranslationsPrefix(): string
    {
        if (is_dir( dirname( __FILE__, 3 ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'menarasolutions')) {
            return dirname( __FILE__, 3 ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'menarasolutions';
        }

        // By default, we assume that language package was installed using Composer
        return dirname( __FILE__, 4 );
    }

    /**
     * @param IdentifiableInterface $subject
     * @param $language
     * @return string
     */
    public function getTranslationsPath(IdentifiableInterface $subject, $language): string
    {
        $elements = explode('\\', get_class($subject));
        $key = strtolower(end($elements));
        $root = $this->getTranslationsPrefix() . DIRECTORY_SEPARATOR . 'geographer-' . $language;

        if (get_class($subject) == City::class) {
            $country = $subject->getMeta()['country'];
            return $root . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $country . '.json';
        }
        
        return $root . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'all.json';
    }

    /**
     * @param string $prefix
     */
    public function setPrefix( string $prefix): void
    {
        $this->prefix = $prefix;
    }


    /**
     * @param       $class
     * @param array $params
     *
     * @return array
     * @throws MisconfigurationException
     */
    public function getData($class, array $params): array
    {
        $file = $this->getPath($class, $this->prefix, $params);

        try {
            $data = $this->loadJson($file);
        } catch (\Exception $e) {
            // Some divisions don't have data files, so we don't want to throw the exception
            return [];
        }

        // ToDo: remove this logic from here
        if ($class === State::class && isset($params['code'])) {
            foreach ($data as $key => $meta) {
                if ($meta['parent'] != $params['code']) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }


    /**
     * @param $path
     * @param $id
     *
     * @return mixed
     * @throws ObjectNotFoundException|FileNotFoundException|MisconfigurationException|ObjectNotFoundException
     */
    protected function getCodeFromIndex($path, $id): mixed
    {
        if (preg_match('/[A-Z]{2}-[A-Z0-9]{2,3}/', $id) === 1) {
            return substr($id, 0, 2);
        }

        if (! isset($this->cache[$path])) {
            $this->cache[ $path ] = $this->loadJson( $path );
        }

        if (! isset($this->cache[$path][$id])) {
            throw new ObjectNotFoundException( 'Cannot find object with id ' . $id );
        }

        return $this->cache[$path][$id];
    }

    /**
     * @param int $id
     * @param string $class
     * @return array
     * @throws ObjectNotFoundException|FileNotFoundException|MisconfigurationException|ObjectNotFoundException
     */
    public function indexSearch($id, $class): array
    {
        $code = $this->getCodeFromIndex($this->prefix . DIRECTORY_SEPARATOR . self::$indexes[$class], $id);

        $key = ($class === State::class) ? 'parentCode' : 'code';
        $path = $this->getPath($class, $this->prefix, [ $key => $code ]);

        if (! isset($this->cache[$path])) {
            $this->cache[$path] = $this->loadJson($path);
        }

        foreach ($this->cache[$path] as $member) {
            if (in_array($id, $member['ids'], false)) {
                return $member;
            }
        }

        throw new ObjectNotFoundException('Cannot find meta for division ' . $id);
    }

    /**
     * @param string $path
     *
     * @return mixed
     * @throws FileNotFoundException|MisconfigurationException
     */
    public function loadJson( string $path): mixed
    {
        if (! file_exists($path)) {
            throw new FileNotFoundException('File not found: ' . $path);
        }
        $decoded = json_decode(file_get_contents($path), true);
        if ($decoded === null) {
            throw new MisconfigurationException('Unable to decode JSON for ' . $path);
        }

        return $decoded;
    }


    /**
     * @param IdentifiableInterface $subject
     * @param                       $language
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function getTranslations(IdentifiableInterface $subject, $language): array
    {
        $path = $this->getTranslationsPath($subject, $language);
        if (empty($this->cache[$path])) {
            $this->loadTranslations($path);
        }

        foreach ($subject->getCodes() as $code) {
            if (isset($this->cache[$path][$code])) {
                return $this->cache[$path][$code];
            }
        }

        return [];
    }

    /**
     * @param string $path
     *
     * @throws FileNotFoundException
     */
    protected function loadTranslations( string $path): void
    {
        $meta = $this->loadJson($path);

        foreach ($meta as $one) {
            $this->cache[$path][$one['code']] = $one;
        }
    }
}
