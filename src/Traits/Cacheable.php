<?php

namespace Gorilla\Traits;

use Carbon\Carbon;
use Gorilla\Contracts\CanCached;
use Illuminate\Support\Arr;
use Phpfastcache\CacheManager;

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
     *
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheLogicException
     * @throws \ReflectionException
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
     * @deprecated Removed user control the cache expire, follow last updated time from server
     *
     * @return $this
     */
    public function cache($seconds = 60)
    {
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
                'current' => Carbon::now()->__toString(),
                'lastUpdatedAt' => data_get($value, '0.last_updated_at'),
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
     * @return \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    public function getCacheInstance()
    {
        return self::$cache;
    }
}
