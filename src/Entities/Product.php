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
     * Category constructor.
     *
     * @param $arguments
     *
     */
    public function __construct($arguments = [])
    {
        parent::__construct($arguments);

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
        $defaultRoutes = '/website/products';


        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        return $defaultRoutes;
    }
}