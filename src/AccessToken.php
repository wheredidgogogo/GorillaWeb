<?php

namespace Gorilla;

use InvalidArgumentException;
use Phpfastcache\CacheManager;

/**
 * Class AccessToken
 *
 * @package Gorilla
 */
class AccessToken
{
    /**
     * @var
     */
    private $accessToken;

    /**
     * @var
     */
    private $expires;

    /**
     * @var string
     */
    private $cacheKey = 'GORILLADASH_CACHED_KEY';

    /**
     * @var \Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    private static $cache;

    /**
     * AccessToken constructor.
     *
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     */
    public function __construct()
    {
        if (!self::$cache) {
            self::$cache = CacheManager::getInstance('files');
        }
    }

    /**
     *
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    public function loadAccessTokenFromCached()
    {
        if ($cache = self::$cache->getItem($this->cacheKey)->get()) {
            $this->fill($cache['accessToken'], $cache['expires']);

            return true;
        }

        return false;
    }

    /**
     * @param $accessToken
     * @param $expires
     *
     * @return $this
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function setup($accessToken, $expires)
    {
        if (!$accessToken) {
            throw new InvalidArgumentException('Access token is required');
        }

        $this->fill($accessToken, $expires);

        $this->accessTokenCached();

        return $this;
    }

    /**
     * @param $accessToken
     * @param $expires
     *
     * @return $this
     */
    public function fill($accessToken, $expires)
    {
        $this->accessToken = $accessToken;
        $this->expires = $expires;

        return $this;
    }

    /**
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     */
    private function accessTokenCached()
    {
        $cache = self::$cache->getItem($this->cacheKey)->set([
            'accessToken' => $this->accessToken,
            'expires' => $this->expires,
        ])->expiresAfter($this->expires);

        self::$cache->save($cache);
    }

    /**
     * Returns the expiration timestamp, if defined.
     *
     * @return integer|null
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @return bool
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function hasExpired()
    {
        if (self::$cache->hasItem($this->cacheKey)) {
            return self::$cache->getItem($this->cacheKey)->isExpired();
        }

        return true;
    }

    /**
     *
     * @throws \RuntimeException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function checkExpired()
    {
        $this->hasExpired();
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
