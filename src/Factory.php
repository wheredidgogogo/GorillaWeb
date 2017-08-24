<?php

namespace Gorilla;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Entities\Category;
use Gorilla\Entities\ComponentType;
use Gorilla\Entities\Menu;
use Gorilla\Entities\Product;
use Gorilla\Entities\Range;
use Gorilla\Entities\WebsiteComponent;
use Gorilla\Entities\WebsiteSection;

/**
 * Class Factory
 *
 * @package Gorilla
 */
class Factory
{
    /**
     * @param $method
     *
     * @param $arguments
     *
     * @return EntityInterface
     */
    public static function create($method, $arguments)
    {
        switch ($method) {
            case 'menus':
                return new Menu($arguments);
            case 'products':
                return new Product($arguments);
            case 'categories':
                return new Category($arguments);
            case 'ranges':
                return new Range($arguments);
            case 'componentType':
                return new ComponentType($arguments);
            case 'websiteSection':
                return new WebsiteSection($arguments);
            case 'websiteComponent':
                return new WebsiteComponent($arguments);
        }

        return null;
    }
}