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
}
