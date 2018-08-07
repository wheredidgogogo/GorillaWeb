<?php

namespace Gorilla\Contracts;

/**
 * Interface EntityInterface
 *
 * @package Gorilla\Contracts
 */
interface EntityInterface
{
    /**
     * Request method type
     *
     * @return string
     */
    public function method();

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters();

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint();

    /**
     * Set Request
     *
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function setRequest(RequestInterface $request);

    /**
     * Get last update at
     *
     * @return string|null
     */
    public function getLastUpdatedAt();

    /**
     * Set last update at
     *
     * @param ?string $lastUpdatedAt
     *
     * @return \Gorilla\Contracts\EntityAbstract
     */
    public function setLastUpdatedAt($lastUpdatedAt);
}
