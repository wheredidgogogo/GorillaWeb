<?php

namespace Gorilla\Contracts;

/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
use Gorilla\Request;
use Gorilla\Traits\Cacheable;

/**
 * Class EntityAbstract
 *
 * @package Gorilla\Contracts
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * EntityAbstract constructor.
     *
     * @param $arguments
     */
    public function __construct($arguments = [])
    {
        if ($this instanceof CanCached) {
            $this->bootCached();
        }
    }

    /**
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Gorilla\Response\JsonResponse|string
     * @throws \RuntimeException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidConfigurationException
     * @throws \phpFastCache\Exceptions\phpFastCacheInvalidArgumentException
     * @throws \phpFastCache\Exceptions\phpFastCacheDriverCheckException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \Gorilla\Exceptions\ResponseException
     */
    public function get()
    {
        return $this->request->request($this);
    }
}
