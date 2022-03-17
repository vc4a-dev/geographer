<?php

namespace MenaraSolutions\Geographer;

use MenaraSolutions\Geographer\Collections\MemberCollection;
use MenaraSolutions\Geographer\Contracts\ManagerInterface;
use MenaraSolutions\Geographer\Services\DefaultManager;

/**
 * Class Country
 * @package MenaraSolutions\FluentGeonames
 */
class Country extends Divisible
{
    /**
     * @var string
     */
    protected string $memberClass = State::class;

    /**
     * @var string
     */
    protected static string|int|null $parentClass = Earth::class;

    /**
     * @var array
     */
    protected array $exposed = [
        'code' => 'ids.iso_3166_1.0',
        'code3' => 'ids.iso_3166_1.1',
        'isoCode' => 'ids.iso_3166_1.0',
        'numericCode' => 'ids.iso_3166_1.2',
        'geonamesCode' => 'ids.geonames',
        'fipsCode' => 'ids.fips',
        'area',
        'currency',
        'phonePrefix' => 'phone',
        'mobileFormat',
        'landlineFormat',
        'trunkPrefix',
        'population',
        'continent',
        'language' => 'languages.0',
        'name'
    ];


    /**
     * @return int|string|null
     */
    public function getParentCode() : int|string|null
    {
        return 'SOL-III';
    }

    /**
     * @return bool|Divisible
     */
    public function getCapital()
    {
        foreach ($this->getStates() as $state) {
            if ($capital = $state->findOne([
                'capital' => true
            ])) {
                return $capital;
            }
        }

        return null;
    }

    /**
     * @return MemberCollection
     */
    public function getStates() : MemberCollection
    {
        return $this->getMembers();
    }

    /**
     * @inheritdoc
     */
    public static function build( int|string $id, ManagerInterface $config = null) : Divisible
    {
        $config = $config ?: new DefaultManager();
        $earth = (new Earth())->setManager($config);

        return $earth->findOneByCode($id);
    }
}
