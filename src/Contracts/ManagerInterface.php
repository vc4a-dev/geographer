<?php

namespace MenaraSolutions\Geographer\Contracts;

/**
 * Interface ManagerInterface
 * @package MenaraSolutions\FluentGeonames\Contracts
 */
interface ManagerInterface
{
    /**
     * @return string
     */
    public function getStoragePath() : string;

    /**
     * @param string $path
     * @return ManagerInterface
     */
    public function setStoragePath(string $path) : ManagerInterface;
    
    /**
     * @return TranslationAgencyInterface
     */
    public function getTranslator() : TranslationAgencyInterface;

    /**
     * @param TranslationAgencyInterface $translator
     * @return ManagerInterface
     */
    public function setTranslator(TranslationAgencyInterface $translator);

    /**
     * @return RepositoryInterface
     */
    public function getRepository() : RepositoryInterface;

    /**
     * @param RepositoryInterface $repository
     * @return ManagerInterface
     */
    public function setRepository(RepositoryInterface $repository) : ManagerInterface;

    /**
     * @param string $form
     */
    public function setForm(string $form);

    /**
     * @return string
     */
    public function getLocale() : string;

    /**
     * @param string $language
     * @return ManagerInterface
     */
    public function setLocale(string $language) : ManagerInterface;

    /**
     * @return ManagerInterface
     */
    public function useShortNames() : ManagerInterface;

    /**
     * @return ManagerInterface
     */
    public function useLongNames() : ManagerInterface;

    /**
     * @return ManagerInterface
     */
    public function includePrepositions() : ManagerInterface;

    /**
     * @return ManagerInterface
     */
    public function excludePrepositions() : ManagerInterface;

    /**
     * @return bool
     */
    public function expectsLongNames() : bool;

    /**
     * @param string $standard
     * @return $this
     */
    public function setStandard(string $standard) : static;

    /**
     * @return string
     */
    public function getStandard() : string;
}