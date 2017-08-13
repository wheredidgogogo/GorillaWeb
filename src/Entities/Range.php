<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class Range
 *
 * @package Gorilla\Entities
 */
class Range extends EntityAbstract
{
    /**
     * @var string|null
     */
    private $slug;

    /**
     * @var string|null
     */
    private $children = null;

    /**
     * Range constructor.
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
        $defaultRoutes = '/website/ranges';


        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        if ($this->children) {
            $defaultRoutes = "{$defaultRoutes}/{$this->children}";
        }

        return $defaultRoutes;
    }

    public function products()
    {
        $this->children = 'products';

        return $this;
    }
}