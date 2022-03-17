<?php

namespace MenaraSolutions\Geographer\Services;

use MenaraSolutions\Geographer\Contracts\IdentifiableInterface;
use MenaraSolutions\Geographer\Contracts\PoliglottaInterface;
use MenaraSolutions\Geographer\Contracts\RepositoryInterface;
use MenaraSolutions\Geographer\Contracts\TranslationAgencyInterface;
use MenaraSolutions\Geographer\Exceptions\MisconfigurationException;
use MenaraSolutions\Geographer\Services\Poliglottas\Danish;
use MenaraSolutions\Geographer\Services\Poliglottas\Dutch;
use MenaraSolutions\Geographer\Services\Poliglottas\French;
use MenaraSolutions\Geographer\Services\Poliglottas\German;
use MenaraSolutions\Geographer\Services\Poliglottas\Mandarin;
use MenaraSolutions\Geographer\Services\Poliglottas\Russian;
use MenaraSolutions\Geographer\Services\Poliglottas\English;
use MenaraSolutions\Geographer\Services\Poliglottas\Spanish;
use MenaraSolutions\Geographer\Services\Poliglottas\Italian;
use MenaraSolutions\Geographer\Services\Poliglottas\Ukrainian;

/**
 * Class TranslationAgency
 * @package MenaraSolutions\FluentGeonames\Services
 */
class TranslationAgency implements TranslationAgencyInterface
{
    /**
     * @var string
     */
    protected string $basePath;

    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * @var string
     */
    protected string $form = 'default';

    /**
     * @var array
     */
    protected array $inflictsTo = [];

    /**
     * @var bool
     */
    protected bool $prepositions = true;

    /**
     * Constants for available languages
     */
    public const LANG_RUSSIAN = 'ru';
    public const LANG_ENGLISH = 'en';
    public const LANG_SPANISH = 'es';
    public const LANG_ITALIAN = 'it';
    public const LANG_FRENCH = 'fr';
    public const LANG_CHINESE = 'zh';
    public const LANG_UKRAINIAN = 'uk';
    public const LANG_GERMAN = 'de';
    public const LANG_DUTCH = 'nl';
    public const LANG_DANISH = 'da';

    /**
     * Constants for available forms
     */
    public const FORM_DEFAULT = 'default';
    public const FORM_IN = 'in';
    public const FORM_FROM = 'from';

    /**
     * List of available translators
     *
     * @var array
     */
    protected array $languages = [
        self::LANG_RUSSIAN => Russian::class,
        self::LANG_ENGLISH => English::class,
        self::LANG_SPANISH => Spanish::class,
        self::LANG_ITALIAN => Italian::class,
        self::LANG_FRENCH => French::class,
        self::LANG_CHINESE => Mandarin::class,
        self::LANG_UKRAINIAN => Ukrainian::class,
        self::LANG_GERMAN => German::class,
        self::LANG_DUTCH => Dutch::class,
        self::LANG_DANISH => Danish::class,
    ];

    /**
     * @var array PoliglottaInterface
     */
    protected array $translators = [];

    /**
     * TranslationRepository constructor.
     *
     * @param string              $basePath
     * @param RepositoryInterface $repository
     */
    public function __construct( string $basePath, RepositoryInterface $repository)
    {
        $this->basePath = $basePath;
        $this->repository = $repository;
    }

    /**
     * @param string $form
     * @return $this
     */
    public function setForm($form) : self
    {
        $this->form = $form;

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
     * @param IdentifiableInterface $subject
     * @param string                $language
     *
     * @return string
     * @throws MisconfigurationException
     */
    public function translate(IdentifiableInterface $subject, $language = 'en') : string
    {
        $translator = $this->getTranslator($language);

        return $translator->translate($subject, $this->form, $this->prepositions);
    }

    /**
     * @param string $language
     *
     * @return PoliglottaInterface
     * @throws MisconfigurationException
     */
    public function getTranslator( string $language) : PoliglottaInterface
    {
        if (! isset($this->languages[$language])) {
            throw new MisconfigurationException('No hablo ' . $language . ', sorry');
        }

        if (! isset($this->translators[$language])) {
            $this->translators[$language] = new $this->languages[$language]($this);
        }

        return $this->translators[$language];
    }

    /**
     * @return RepositoryInterface $repository
     */
    public function getRepository() : RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @param RepositoryInterface $repository
     *
     * @return TranslationAgencyInterface
     */
    public function setRepository( RepositoryInterface $repository ) : self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedLanguages() : array
    {
        return array_keys($this->translators);
    }
}
