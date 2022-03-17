<?php

namespace MenaraSolutions\Geographer\Contracts;

/**
 * Interface TranslationRepositoryInterface
 * @package MenaraSolutions\FluentGeonames\Contracts
 */
interface TranslationAgencyInterface
{
    /**
     * @param IdentifiableInterface $subject
     * @param string $language
     * @return string
     */
    public function translate(IdentifiableInterface $subject, string $language) : string;

    /**
     * @return RepositoryInterface $repository
     */
    public function getRepository() : RepositoryInterface;

    /**
     * @param RepositoryInterface $repository
     *
     * @return TranslationAgencyInterface
     */
    public function setRepository(RepositoryInterface $repository) : TranslationAgencyInterface;

    /**
     * @return array
     */
    public function getSupportedLanguages() : array;

    /**
     * @param $form
     * @return TranslationAgencyInterface
     */
    public function setForm($form) : TranslationAgencyInterface;

    /**
     * @return TranslationAgencyInterface
     */
    public function includePrepositions() : TranslationAgencyInterface;

    /**
     * @return TranslationAgencyInterface
     */
    public function excludePrepositions() : TranslationAgencyInterface;
}