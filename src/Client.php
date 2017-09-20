<?php

namespace Gorilla;

use Gorilla\Entities\GraphQL;
use Gorilla\Exceptions\NonExistMethodException;
use Gorilla\GraphQL\Collection;
use Gorilla\Response\JsonResponse;

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
    }

    public function get()
    {
        return $this->request->request(new GraphQL($this->queries));
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
}
