<?php

namespace MenaraSolutions\Geographer;

use MenaraSolutions\Geographer\Collections\MemberCollection;
use MenaraSolutions\Geographer\Contracts\ManagerInterface;
use MenaraSolutions\Geographer\Contracts\IdentifiableInterface;
use MenaraSolutions\Geographer\Services\DefaultManager;
use MenaraSolutions\Geographer\Traits\ExposesFields;
use MenaraSolutions\Geographer\Traits\HasManager;
use MenaraSolutions\Geographer\Traits\HasCollection;

/**
 * Class Divisible
 * @package App
 */
abstract class Divisible implements IdentifiableInterface, \ArrayAccess
{
    use HasManager, ExposesFields, HasCollection;

    /**
     * @var array $meta
     */
    protected array $meta;

    /**
     * @var null|MemberCollection $members
     */
    protected null|MemberCollection $members;

    /**
     * @var string $memberClass
     */
    protected string $memberClass;

    /**
     * @var int|string|null $parentClass
     */
    protected static string|int|null $parentClass;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $manager;

    /**
     * @var Divisible
     */
    private Divisible $parent;

    /**
     * @var null|string
     */
    protected ?string $parentCode;

    /**
     * @var string
     */
    protected string $standard;

    /**
     * @var array
     */
    protected array $exposed = [];


    /**
     * Country constructor.
     *
     * @param array                 $meta
     * @param string|null           $parentCode
     * @param ManagerInterface|null $manager
     */
    public function __construct( array $meta = [], string $parentCode = null, ManagerInterface $manager = null)
    {
        $this->meta = $meta;
        $this->parentCode = $parentCode;
        $this->manager = $manager ?: new DefaultManager();
    }


    /**
     * @return MemberCollection|null
     */
    public function getMembers() : ?MemberCollection
    {
        if (! isset( $this->members ) ) {
            $this->loadMembers();
        }

        return $this->members;
    }


    /**
     * @param MemberCollection|null $collection
     *
     * @return void
     */
    protected function loadMembers(MemberCollection $collection = null) : void
    {
        $standard = $this->standard ?? $this->manager->getStandard();

        $data = $this->manager->getRepository()->getData(get_class($this), [
            'code' => $this->getCode(), 'parentCode' => $this->getParentCode()
        ]);

        $collection = $collection ?: (new MemberCollection($this->manager));

        foreach($data as $meta) {
            $entity = new $this->memberClass($meta, $this->getCode(), $this->manager);

            if (! empty($entity[$standard . 'Code'])) {
                $collection->add($entity, $entity[$standard . 'Code']);
            }
        }

        $this->members = $collection;
    }

    /**
     * Best effort name
     *
     * @param string|null $locale
     *
     * @return string
     */
    public function getName( string $locale = null) : string
    {
        if ($locale) {
            $this->setLocale( $locale );
        }

        return $this->manager->expectsLongNames() ? $this->getLongName() : $this->getShortName();
    }


    /**
     * @return string
     */
    public function getShortName() : string
    {
        $this->manager->useShortNames();

        return $this->translate();
    }

    /**
     * @return string
     */
    public function getLongName() : string
    {
        $this->manager->useLongNames();

        return $this->translate();
    }

    /**
     * @return bool
     */
    public function expectsLongNames() : bool
    {
        return $this->manager->expectsLongNames();
    }

    /**
     * @return Divisible
     */
    public function parent() : Divisible
    {
        if (! $this->parent) {
            $this->parent = call_user_func([static::$parentClass, 'build'], $this->parentCode, $this->manager);
        }

        return $this->parent;
    }

    /**
     * @return array
     */
    public function getMeta() : array
    {
        return $this->meta;
    }

    /**
     * @return null|string|int
     */
    public function getParentCode() : int|string|null
    {
        return $this->meta['parent'];
    }

    /**
     * @param string|null $locale
     *
     * @return string
     */
    public function translate( string $locale = null) : string
    {
        if ($locale) {
            $this->manager->setLocale($locale);
        }

        return $this->manager->getTranslator()
            ->translate($this, $this->manager->getLocale());
    }

    /**
     * @param int|string            $id
     * @param ManagerInterface|null $config
     *
     * @return Divisible City|Country|State
     */
    public static function build( int|string $id, ManagerInterface $config = null) : Divisible
    {
        $config = $config ?: new DefaultManager();
        $meta = $config->getRepository()
            ->indexSearch($id, static::$parentClass);
        $parent = $meta['parent'] ?? null;

        return new static($meta, $parent, $config);
    }

    /**
     * @return array
     */
    public function getCodes() : array
    {
        $codes = [];
        array_walk_recursive($this->meta['ids'], static function( $id) use (&$codes) { $codes[] = $id; });

        return $codes;
    }
}
