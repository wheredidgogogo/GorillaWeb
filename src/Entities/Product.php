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
     * @var string|null
     */
    private $slug;

    /**
     * Product constructor.
     *
     * @param null $slug
     */
    public function __construct($slug = null)
    {
        $this->slug = $slug;
    }

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
        $defaultRoutes = '/website/products';


        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        return $defaultRoutes;
    }
}