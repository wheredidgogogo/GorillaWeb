<?php

namespace Gorilla;

use Gorilla\Contracts\EntityInterface;
use Gorilla\Entities\Category;
use Gorilla\Entities\Menu;
use Gorilla\Entities\Product;
use Gorilla\Entities\Range;

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
        }

        return null;
    }
}