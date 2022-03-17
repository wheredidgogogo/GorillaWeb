<?php

namespace Gorilla;

use Gorilla\Entities\GraphQL;
use Gorilla\Exceptions\NonExistMethodException;
use Gorilla\GraphQL\Collection;
use Gorilla\Response\JsonResponse;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

/**
 * Class Client
 *
 * @package Gorilla
 */
class Client
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Collection
     */
    private $queries;

    /**
     * @var string
     */
    private $cachePath = '/tmp';

    /**
     * @var int
     */
    private $cacheSeconds = 0;

    /**
     * @var int
     */
    private $defaultCacheSeconds = 60;

    /**
     * Client constructor.
     *
     * @param $id
     * @param $token
     *
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     * @throws \ReflectionException
     */
    public function __construct($id, $token)
    {
        $this->request = new Request($id, $token);
        $this->queries = new Collection();
        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => $this->cachePath,
        ]));
    }

    /**
     * @return JsonResponse|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     */
    public function get()
    {
        $graphQL = new GraphQL($this->queries);

        $response = $this->request->request($graphQL);
        $this->queries->reset();

        return $response;
    }

    /**
     * @param $seconds
     *
     * @return $this
     */
    public function setDefaultCacheSecond($seconds)
    {
        $this->defaultCacheSeconds = $seconds;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheSeconds()
    {
        return $this->cacheSeconds;
    }

    /**
     * Set cache seconds
     *
     * @param $seconds
     *
     * @return $this
     */
    public function cache($seconds = null)
    {
        $this->cacheSeconds = $seconds ?: $this->defaultCacheSeconds;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheSeconds > 0;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return JsonResponse|mixed|string
     * @throws \Gorilla\Exceptions\NonExistMethodException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->request, $name)) {
            return \call_user_func_array([$this->request, $name], $arguments);
        }

        if (method_exists($this->queries, $name)) {
            call_user_func_array([$this->queries, $name], $arguments);

            return $this;
        }

        $entity = Factory::create($name, $arguments);
        if ($entity) {
            return $entity->setRequest($this->request);
        }

        throw new NonExistMethodException("Sorry, we didn't find ${name}");
    }

    /**
     * @param $path
     *
     * @return $this
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     * @throws \ReflectionException
     */
    public function setCachePath($path)
    {
        $this->cachePath = $path;
        CacheManager::setDefaultConfig(new ConfigurationOption(['path' => $path]));

        return $this;
    }
}
