<?php

namespace Gorilla\Contracts;

/**
 * Interface CanCached
 *
 * @package Gorilla\Contracts
 */
interface CanCached
{
    /**
     * Boot cache
     *
     * @return mixed
     */
    public function bootCached();

    /**
     * Get cache data
     *
     * @return mixed
     */
    public function getCached();

    /**
     * Merge cache and response data
     *
     * @param array $array
     *
     * @return mixed
     */
    public function merge(array $array);

    /**
     * All data cached
     *
     * @return mixed
     */
    public function allInCached();

    /**
     * Save cache
     *
     * @param $data
     *
     * @return mixed
     */
    public function saveCache($data);

    /**
     * Get last updated time and cached time
     *
     * @return array
     */
    public function getCachedTime($key);
}
