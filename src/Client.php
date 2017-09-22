<?php

namespace Gorilla;

use Gorilla\Entities\GraphQL;
use Gorilla\Exceptions\NonExistMethodException;
use Gorilla\GraphQL\Collection;
use Gorilla\Response\JsonResponse;
use phpFastCache\CacheManager;

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
     * Client constructor.
     *
     * @param $id
     * @param $token
     *
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     */
    public function __construct($id, $token)
    {
        $this->request = new Request($id, $token);
        $this->queries = new Collection();
        CacheManager::setDefaultConfig([
            'path' => $this->cachePath,
            'ignoreSymfonyNotice' => true,
        ]);
    }

    /**
     * @return JsonResponse|string
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \Gorilla\Exceptions\ResponseException
     */
    public function get()
    {
        $graphQL = new GraphQL($this->queries);

        return $this->request->request($graphQL);
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
            return call_user_func_array([$this->request, $name], $arguments);
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
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     */
    public function setCachePath($path)
    {
        $this->cachePath = $path;
        CacheManager::setDefaultConfig('path', $path);

        return $this;
    }
}
