<?php

namespace MenaraSolutions\Geographer;

/**
 * Class City
 * @package MenaraSolutions\Geographer
 */
class City extends Divisible
{
    /**
     * @var string
     */
    protected string $memberClass;

    /**
     * @var string|int|null
     */
    protected static string|int|null $parentClass = State::class;

    /**
     * @var array
     */
    protected array $exposed = [
        'code' => 'ids.geonames',
        'geonamesCode' => 'ids.geonames',
        'name',
        'latitude' => 'lat',
        'longitude' => 'lng',
        'population',
        'capital'
    ];
}
