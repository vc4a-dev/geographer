<?php

namespace MenaraSolutions\Geographer;

use MenaraSolutions\Geographer\Collections\MemberCollection;
use MenaraSolutions\Geographer\Contracts\ManagerInterface;
use MenaraSolutions\Geographer\Services\DefaultManager;

class Earth extends Divisible
{
    /**
     * @var string
     */
    protected string $memberClass = Country::class;

    /**
     * @var int|string|null
     */
    protected static string|int|null $parentClass = null;

    /**
     * @var array
     */
    protected array $exposed = [
        'code',
        'name'
    ];

    /**
     * @return MemberCollection
     */
    public function getCountries() : MemberCollection
    {
        return $this->getMembers();
    }

    /**
     * @return string
     */
    public function getShortName() : string
    {
        return 'Earth';
    }

    /**
     * @return string
     */
    public function getLongName() : string
    {
        return 'The Blue Marble';
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return 'SOL-III';
    }


    /**
     * @return int|string|null
     */
    public function getParentCode() : int|string|null
    {
        return null;
    }

    /**
     * @return MemberCollection
     */
    public function getAfrica() : MemberCollection
    {
        return $this->find([
            'continent' => 'AF'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getNorthAmerica() : MemberCollection
    {
        return $this->find([
            'continent' => 'NA'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getSouthAmerica() : MemberCollection
    {
        return $this->find([
            'continent' => 'SA'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getAsia() : MemberCollection
    {
        return $this->find([
            'continent' => 'AS'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getEurope() : MemberCollection
    {
        return $this->find([
            'continent' => 'EU'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function getOceania() : MemberCollection
    {
        return $this->find([
            'continent' => 'OC'
        ]);
    }

    /**
     * @return MemberCollection
     */
    public function withoutMicro()
    {
        return $this->getMembers()->filter(function($item) {
            return $item->getPopulation() > 100000;
        });
    }

    /**
     * @inheritdoc
     */
    public static function build( int|string $id, ManagerInterface $config = null) : self
    {
        $config = $config ?: new DefaultManager();
        return new static([], null, $config);
    }
}