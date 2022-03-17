<?php

namespace MenaraSolutions\Geographer\Traits;

use MenaraSolutions\Geographer\Contracts\ManagerInterface;

/**
 * Class HasManager
 * @package MenaraSolutions\FluentGeonames\Traits
 */
trait HasManager
{
    /**
     * @return ManagerInterface
     */
    public function getManager() : ManagerInterface
    {
        return $this->manager;
    }

    /**
     * @param ManagerInterface $manager
     * @return $this
     */
    public function setManager(ManagerInterface $manager) : self
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale( string $locale) : self
    {
        $this->manager->setLocale($locale);

        return $this;
    }

    /**
     * @param string $standard
     *
     * @return $this
     */
    public function setStandard( string $standard) : self
    {
        $this->manager->setStandard($standard);
        $this->members = null;

        return $this;
    }

    /**
     * @param string $form
     * @return $this
     */
    public function inflict( string $form ) : self
    {
        $this->manager->setForm($form);

        return $this;
    }

    /**
     * @return $this
     */
    public function useLongNames() : self
    {
        $this->manager->useLongNames();

        return $this;
    }

    /**
     * @return $this
     */
    public function useShortNames() : self
    {
        $this->manager->useShortNames();

        return $this;
    }

    /**
     * @return $this
     */
    public function excludePrepositions() : self
    {
        $this->manager->excludePrepositions();

        return $this;
    }

    /**
     * @return $this
     */
    public function includePrepositions() : self
    {
        $this->manager->includePrepositions();

        return $this;
    }
}