<?php

namespace Gorilla;

use Gorilla\Exceptions\NonExistMethodException;
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

        $entity = Factory::create($name, $arguments);
        if ($entity) {
            return $entity->setRequest($this->request);
        }

        throw new NonExistMethodException("Sorry, we didn't find ${name}");
    }
}
