<?php

namespace Gorilla\Traits;

use Gorilla\Contracts\CanCached;
use phpFastCache\CacheManager;

/**
 * Trait Cacheable
 *
 * @package Gorilla\Traits
 */
trait Cacheable
{
    /**
     * @var \phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    protected static $cache;

    /**
     * @var int
     */
    protected $cacheTime = 0;

    /**
     * Cache file
     *
     * @var array
     */
    private $cacheData = [];

    /**
     *
     * @throws \RuntimeException
     */
    public function bootCached()
    {
        if (!$this instanceof CanCached) {
            throw new \RuntimeException('Missing implement '.CanCached::class.' interface');
        }

        if (!self::$cache) {
            self::$cache = CacheManager::getInstance('files');
        }
    }

    /**
     * @param int $seconds
     *
     * @return $this
     */
    public function cache($seconds = 60)
    {
        $this->cacheTime = $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->cacheTime;
    }

    /**
     * Get cache
     *
     * @param $key
     *
     * @return mixed|null
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function getCached($key)
    {
        $item = self::$cache->getItem($key);
        if ($item->isExpired() || !self::$cache->hasItem($key)) {
            return null;
        }

        return self::$cache->getItem($key)->get();
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function merge(array $array)
    {
        return array_merge_recursive($this->cacheData, $array);
    }

    /**
     * @return array
     */
    public function getCacheData()
    {
        return $this->cacheData;
    }

    /**
     *
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function saveCacheData($key, $value)
    {
        return self::$cache->save(self::$cache->getItem($key)->set($value));
    }
}