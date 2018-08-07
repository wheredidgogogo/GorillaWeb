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
     * Category constructor.
     *
     * @param $arguments
     *
     */
    public function __construct($arguments = [])
    {
        parent::__construct();

        if (count($arguments) > 0) {
            $this->slug = $arguments[0];
        }
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
