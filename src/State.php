<?php

namespace MenaraSolutions\Geographer;

/**
 * Class State
 * @package MenaraSolutions\FluentGeonames
 */
class State extends Divisible
{
    /**
     * @var string
     */
    protected string $memberClass = City::class;

    /**
     * @var string
     */
    protected static string|int|null $parentClass = Country::class;

    /**
     * @var string
     */
    protected string $standard = 'geonames';

    /**
     * @var array
     */
    protected array $exposed = [
        'code' => 'ids.geonames',
        'fipsCode' => 'ids.fips',
        'isoCode' => 'ids.iso_3166_2',
        'geonamesCode' => 'ids.geonames',
        'postCodes' => 'postcodes',
        'name',
        'timezone'
    ];


    /**
     * @return Collections\MemberCollection|null
     */
    public function getCities() : ?Collections\MemberCollection
    {
        return $this->getMembers();
    }
}
