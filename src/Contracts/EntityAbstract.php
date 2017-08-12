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
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Gorilla\Response\JsonResponse|string
     */
    public function get()
    {
        return $this->request->request($this);
    }
}
