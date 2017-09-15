<?php

namespace Gorilla;

use InvalidArgumentException;
use phpFastCache\CacheManager;
use RuntimeException;

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
     * @var \phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface
     */
    private static $cache;

    /**
     * AccessToken constructor.
     *
     * @internal param $accessToken
     * @internal param $expires
     * @internal param $refreshToken
     *
     * @param $cachePath
     *
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     */
    public function __construct($cachePath)
    {
        CacheManager::setDefaultConfig([
            'path' => $cachePath,
            'ignoreSymfonyNotice' => true,
        ]);
        if (!self::$cache) {
            self::$cache = CacheManager::getInstance('files');
        }
    }

    /**
     *
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function loadAccessTokenFromCached()
    {
        if ($cache = self::$cache->getItem($this->cacheKey)->get()) {

            $this->fill($cache['accessToken'] , $cache['expires']);

            return true;
        }

        return false;
    }

    /**
     * @param $accessToken
     * @param $expires
     *
     * @return $this
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function setup($accessToken, $expires)
    {
        if (!$accessToken) {
            throw new InvalidArgumentException('Access token is required');
        }

        $expires = $expires !== 0 ? time() + $expires : 0;
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
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
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
     * @throws \RuntimeException
     */
    public function hasExpired()
    {
        $expires = $this->getExpires();
        if (null === $expires) {
            throw new RuntimeException('"expires" is not set on the token');
        }
        return $expires < time();
    }

    /**
     *
     * @throws \RuntimeException
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