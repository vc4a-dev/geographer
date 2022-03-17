<?php

namespace MenaraSolutions\Geographer\Contracts;

/**
 * Interface IdentifiableInterface
 * @package MenaraSolutions\FluentGeonames\Contracts
 */
interface IdentifiableInterface
{
    /**
     * @return bool
     */
    public function expectsLongNames() : bool;

    /**
     * @return array
     */
    public function getMeta() : array;

    /**
     * Get an array of unique identification codes for this object
     *
     * @return array
     */
    public function getCodes() : array;
}