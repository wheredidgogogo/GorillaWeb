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
        $defaultRoutes = '/website/menus';

        if ($this->slug) {
            $defaultRoutes = "{$defaultRoutes}/{$this->slug}";
        }

        return $defaultRoutes;
    }

    /**
     * @param $id
     *
     * @return SubMenu
     */
    public function sub($id)
    {
        return new SubMenu($this->request, [$id]);
    }
}
