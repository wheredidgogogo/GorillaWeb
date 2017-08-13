<?php

namespace Gorilla\Entities;

use Gorilla\Contracts\EntityAbstract;
use Gorilla\Contracts\MethodType;

/**
 * Class Menu
 *
 * @package Gorilla\Entities
 */
class Menu extends EntityAbstract
{
    /**
     * @var string|null
     */
    private $slug;

    /**
     * Menu constructor.
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
        $defaultRoutes = '/website/menus';


        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        return $defaultRoutes;
    }
}
