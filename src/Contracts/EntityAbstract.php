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
     * @var ?string
     */
    protected $lastUpdatedAt;

    /**
     * EntityAbstract constructor.
     *
     */
    public function __construct()
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
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \Gorilla\Exceptions\ResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get()
    {
        return $this->request->request($this);
    }

    /**
     * @return mixed
     */
    public function getLastUpdatedAt()
    {
        return $this->lastUpdatedAt;
    }

    /**
     * @param mixed $lastUpdatedAt
     *
     * @return \Gorilla\Contracts\EntityAbstract
     */
    public function setLastUpdatedAt($lastUpdatedAt)
    {
        $this->lastUpdatedAt = $lastUpdatedAt;
        return $this;
    }
}
