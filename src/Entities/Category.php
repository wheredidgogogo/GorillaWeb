<?php


namespace Gorilla\Entities;


use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class Category
 *
 * @package Gorilla\Entities
 */
class Category extends EntityAbstract
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
     * @param $slug
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
        $defaultRoutes = '/website/categories';


        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        if ($this->children) {
            $defaultRoutes = "{$defaultRoutes}/{$this->children}";
        }

        return $defaultRoutes;
    }

    /**
     * Get products
     *
     * @return $this
     */
    public function products()
    {
        $this->children = 'products';

        return $this;
    }

    /**
     * Get ranges
     *
     * @return $this
     */
    public function ranges()
    {
        $this->children = 'ranges';

        return $this;
    }
}