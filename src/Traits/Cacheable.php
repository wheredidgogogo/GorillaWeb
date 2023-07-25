<?php

namespace Gorilla\Traits;

use Carbon\Carbon;
use Gorilla\Contracts\CanCached;
use Illuminate\Support\Arr;
use Phpfastcache\CacheManager;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;

/**
 * Trait Cacheable
 *
 * @package Gorilla\Traits
 */
trait Cacheable
{
    /**
     * @var \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    protected static $cache;

    /**
     * @var int
     */
    protected $cacheTime = 31536000;

    /**
     * Cache file
     *
     * @var array
     */
    private $cacheData = [];

    /**
     * @var bool
     */
    private $handleCacheByClient = false;

    /**
     *
     * @throws PhpfastcacheDriverCheckException
     * @throws PhpfastcacheDriverException
     * @throws PhpfastcacheDriverNotFoundException
     * @throws PhpfastcacheLogicException
     */
    public function bootCached()
    {
        if (!$this instanceof CanCached) {
            throw new \RuntimeException('Missing implement ' . CanCached::class . ' interface');
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
        if ($this->handleCacheByClient) {
            $this->cacheTime = $seconds;
        }
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setHandleCacheByClient($value)
    {
        $this->handleCacheByClient = $value;

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
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCached($key)
    {
        $item = self::$cache->getItem($key);
        if ($item->isExpired() || !self::$cache->hasItem($key)) {
            return null;
        }

        return data_get($item->get(), 'data');
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
     * @param $key
     * @param $value
     *
     * @return bool
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    public function saveCacheData($key, $value)
    {
        if ($this->cacheTime) {
            $cached = [
                'data' => $value,
            ];

            return self::$cache->save(self::$cache->getItem($key)->set($cached)->expiresAfter($this->cacheTime));
        }

        return true;
    }

    /**
     * Get last updated time and cached time
     *
     * @param $key
     *
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    public function getCachedTime($key)
    {
        $item = self::$cache->getItem($key);
        if ($item->isExpired() || !self::$cache->hasItem($key)) {
            return null;
        }

        $cacheData = $item->get();

        return Arr::only($cacheData, ['lastUpdatedAt', 'current']);
    }

    /**
     * @return ExtendedCacheItemPoolInterface
     */
    public function getCacheInstance()
    {
        return self::$cache;
    }
}
