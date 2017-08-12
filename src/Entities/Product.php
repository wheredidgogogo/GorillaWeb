<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class Product
 *
 * @package Gorilla\Entities
 */
class Product extends EntityAbstract
{

    /**
     * Request method type
     *
     * @return string
     */
    public function method()
    {
        return MethodType::GET;
    }

    /**
     * Request parameters
     *
     * @return array
     */
    public function parameters()
    {
        return [];
    }

    /**
     * Endpoint url
     *
     * @return string
     */
    public function endpoint()
    {
        return $this->buildEndpoint();
    }

    /**
     * @return string
     */
    private function buildEndpoint()
    {
        if ($name = $this->parameters['name']) {
            return "/website/products/{$name}";
        }

        return '/website/products';
    }

    /**
     * @param null $name
     *
     * @return \Gorilla\Response\JsonResponse|string
     */
    public function get($name = null)
    {
        if ($name) {
            $this->parameters['name'] = $name;
        }

        return parent::get();
    }
}