<?php

namespace MenaraSolutions\Geographer\Contracts;

/**
 * Interface RepositoryInterface
 * @package MenaraSolutions\Geographer\Contracts
 */
interface RepositoryInterface
{
    /**
     * @param string $class
     * @param array $params
     * @return array
     */
    public function getData($class, array $params) : array;

    /**
     * @param IdentifiableInterface $subject
     * @param $language
     * @return array
     */
    public function getTranslations(IdentifiableInterface $subject, $language) : array;

    /**
     * @param $id
     * @param $class
     * @return mixed
     */
    public function indexSearch($id, $class) : mixed;
}