<?php

namespace MenaraSolutions\Geographer\Services;

use MenaraSolutions\Geographer\Contracts\ManagerInterface;
use MenaraSolutions\Geographer\Contracts\RepositoryInterface;
use MenaraSolutions\Geographer\Contracts\TranslationAgencyInterface;
use MenaraSolutions\Geographer\Repositories\File;

/**
 * Class DefaultManager
 * @package MenaraSolutions\FluentGeonames\Services
 */
class DefaultManager implements ManagerInterface
{
    /**
     * Supported subdivision standards
     */
    public const STANDARD_ISO = 'iso';
    public const STANDARD_FIPS = 'fips';
    public const STANDARD_GEONAMES = 'geonames';

    /**
     * @var TranslationAgencyInterface $translator
     */
    protected TranslationAgencyInterface $translator;

    /**
     * @var RepositoryInterface $repository
     */
    protected RepositoryInterface $repository;

    /**
     * @var string
     */
    protected string $language = 'en';

    /**
     * @var string
     */
    protected string $form;

    /**
     * @var string
     */
    protected string $standard = self::STANDARD_ISO;

    /**
     * @var bool
     */
    protected bool $brief = true;

    /**
     * @var bool
     */
    protected bool $prepositions = true;

    /**
     * @var string
     */
    protected string $path;


    /**
     * DefaultConfig constructor.
     *
     * @param null                            $path
     * @param TranslationAgencyInterface|null $translator
     * @param RepositoryInterface|null        $repository
     */
    public function __construct($path = null, TranslationAgencyInterface $translator = null, RepositoryInterface $repository= null)
    {
        $this->path = $path ?: self::getDefaultPrefix();
        $this->repository = $repository ?: new File();
        $this->translator = $translator ?: new TranslationAgency($this->path, $this->repository);
    }

    /**
     * @return string
     */
    public static function getDefaultPrefix() : string
    {
        return dirname( __FILE__, 3 ) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getStoragePath() : string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setStoragePath($path) : self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return TranslationAgencyInterface
     */
    public function getTranslator() : TranslationAgencyInterface
    {
        $this->prepositions
            ? $this->translator->includePrepositions()
            : $this->translator->excludePrepositions();

        return $this->translator;
    }

    /**
     * @param TranslationAgencyInterface $translator
     * @return $this
     */
    public function setTranslator(TranslationAgencyInterface $translator) : self
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository() : RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @param RepositoryInterface $repository
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository) : self
    {
        $this->repository = $repository;
        $this->translator->setRepository($repository);

        return $this;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale( string $locale ) : self
    {
        $this->language = strtolower(substr($locale, 0, 2));

        return $this;
    }

    /**
     * @param string $form
     * @return $this
     */
    public function setForm( string $form ) : self
    {
        $this->translator->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale() : string
    {
        return $this->language;
    }

    /**
     * @return $this
     */
    public function useLongNames() : self
    {
        $this->brief = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function useShortNames() : self
    {
        $this->brief = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function includePrepositions() : self
    {
        $this->prepositions = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function excludePrepositions() : self
    {
        $this->prepositions = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function expectsLongNames() : bool
    {
        return ! $this->brief;
    }

    /**
     * @return string
     */
    public function getStandard() : string
    {
        return $this->standard;
    }

    /**
     * @param string $standard
     * @return $this
     */
    public function setStandard( string $standard ) : self
    {
        $this->standard = $standard;

        return $this;
    }
}
