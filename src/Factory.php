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
     * @return EntityInterface
     */
    public static function create($method)
    {
        switch ($method) {
            case 'menus':
                return new Menu();
            case 'products':
                return new Product();
            case 'categories':
                return new Category();
            case 'ranges':
                return new Range();
        }

        return null;
    }
}